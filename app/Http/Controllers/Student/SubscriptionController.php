<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plan;
use App\Services\StripeService;
use App\Traits\StripeTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    use StripeTrait;

    protected $view_path = 'student.inner.subscription';
    protected StripeService $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
        $this->middleware('auth');
    }

    public function index()
    {
        $monthlyPlans = Plan::active()->where('interval', 'monthly')->get();
        $trialPlan = Plan::active()->where('interval', 'one_time')->first();

        return view($this->view_path . '.pricing', [
            'monthlyPlans' => $monthlyPlans,
            'trialPlan' => $trialPlan,
        ]);
    }

    /**
     * Start a Stripe Checkout session for the given plan slug.
     *
     * Uses the injected StripeService for plan/price creation and the trait's stripe() client to create the session.
     */
    public function checkout(Request $request, string $slug)
    {
        $user = Auth::user();

        // Find plan or 404
        $plan = Plan::where('slug', $slug)->active()->firstOrFail();

        // Ensure Stripe customer exists (Cashier helper)
        if (! $user->hasStripeId()) {
            $user->createOrGetStripeCustomer();
        }

        // Ensure plan has a stripe_price_id; create on Stripe if necessary
        $priceId = $plan->stripe_price_id;
        if (! $priceId) {
            $payload = [
                'name' => $plan->name . ($plan->interval === 'one_time' ? ' (One-Time)' : ' ' . $plan->interval),
                'description' => $plan->description,
                'amount' => (int) $plan->price,
                'currency' => $plan->interval === 'one_time' ? 'usd' : 'jpy', // adjust as needed
                'interval' => $plan->interval,
                'slug' => $plan->slug,
                'metadata' => [
                    'local_plan_slug' => $plan->slug
                ],
            ];

            $result = $this->stripeService->createPlan($payload);

            if (isset($result['error'])) {
                Log::error('Stripe createPlan error', ['plan' => $plan->slug, 'error' => $result['error']]);
                return back()->with('error', 'Payment setup error: ' . $result['error']);
            }

            $priceId = $result['price_id'] ?? null;
            if (! $priceId) {
                Log::error('Stripe createPlan returned no price_id', ['result' => $result, 'plan' => $plan->slug]);
                return back()->with('error', 'Unable to prepare payment for this plan. Please contact support.');
            }

            // Persist stripe ids
            $plan->update([
                'stripe_product_id' => $result['product_id'] ?? $plan->stripe_product_id,
                'stripe_price_id' => $priceId,
            ]);
        }

        // Initialize global key and get client from trait
        try {
            $this->initStripe();          // ensures Stripe::setApiKey(...) is set
            $stripeClient = $this->stripe(); // <-- uses the trait's stripe() accessor
        } catch (\Throwable $e) {
            Log::error('Stripe initialization failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Payment system unavailable. Please try again later.');
        }

        // Build checkout session
        try {
            $successUrl = route('student.subscription.success') . '?session_id={CHECKOUT_SESSION_ID}';
            $cancelUrl = route('student.subscription.cancel');
            $mode = $plan->interval === 'one_time' ? 'payment' : 'subscription';

            $sessionParams = [
                'customer' => $user->stripe_id,
                'payment_method_types' => ['card'],
                'line_items' => [[ 'price' => $priceId, 'quantity' => 1 ]],
                'mode' => $mode,
                'metadata' => [
                    'local_user_id' => $user->id,
                    'local_plan_slug' => $plan->slug,
                ],
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
            ];

            $session = $stripeClient->checkout->sessions->create($sessionParams);

            if (! empty($session->url)) {
                return redirect($session->url);
            }

            // fallback for older SDKs
            return redirect()->away("https://checkout.stripe.com/pay/{$session->id}");
        } catch (\Throwable $e) {
            Log::error('Stripe Checkout creation failed', [
                'user_id' => $user->id,
                'plan' => $plan->slug,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Could not start checkout: ' . $e->getMessage());
        }
    }

    public function success(Request $request)
    {   
        dd('success');
        return view($this->view_path . '.success');
    }

    public function cancel()
    {   
        dd('cancel');
        return view($this->view_path . '.cancel');
    }
}
