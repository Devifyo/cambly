{{-- ... header code ... --}}

<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="menu-title"> 
                    <span>Main</span>
                </li>

                {{-- DASHBOARD --}}
                @can('view_dashboard')
                <li class="{{ role_route_active('admin.dashboard') }}"> 
                    <a href="{{ role_route('admin.dashboard') }}"><i class="fe fe-home"></i> <span>Dashboard</span></a>
                </li>
                @endcan

                {{-- ADMINS (Manage Subadmins) --}}
                @can('view_admins')
                    <li class="{{ role_route_active('admin.subadmins.index') }}"> 
                        <a href="{{role_route('admin.subadmins.index')}}"><i class="fe fe-shield"></i> <span>Admins</span></a>
                    </li>
                @endcan

                {{-- OPS --}}
                @can('view_ops')
                <li class="{{ role_route_active('admin.ops.index') }}"> 
                    <a href="{{ role_route('admin.ops.index') }}"><i class="fas fa-user-cog"></i> <span>Ops</span></a>
                </li>
                @endcan

                {{-- TEACHERS --}}
                @can('view_teachers')
                <li class="{{ role_route_active('admin.teachers.index') }}"> 
                    <a href="{{ role_route('admin.teachers.index') }}"><i class="fe fe-user-plus"></i> <span>Teachers</span></a>
                </li>
                @endcan

                {{-- STUDENTS --}}
                @can('view_students')
                <li class="{{ role_route_active('admin.students.index') }}"> 
                    <a href="{{ role_route('admin.students.index') }}"><i class="fas fa-user-graduate"></i> <span>Students</span></a>
                </li>
                @endcan

                {{-- SUBSCRIPTIONS --}}
                @can('view_subscriptions')
                <li class="{{ role_route_active('admin.subscription.plan.index') }}"> 
                    <a href="{{route('admin.subscription.plan.index')}}"><i class="fe fe-credit-card"></i> <span>Subscription Plans</span></a>
                </li>
                @endcan
                
                {{-- SETTINGS (Link to the new Role Manager) --}}
                @can('manage_permissions')
                <li class="{{ role_route_active('admin.settings.roles') }}"> 
                    <a href="{{role_route('admin.settings.roles')}}"><i class="fa fas fa-user-shield"></i> <span>Roles & Perms</span></a>
                </li>
                @endcan

            </ul>
        </div>
    </div>
</div>