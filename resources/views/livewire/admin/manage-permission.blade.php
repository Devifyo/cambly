<div class="content container-fluid">
    <livewire:admin.components.alert-handler />
    
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Role & Permission Matrix</h3>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 30%;">Module / Permission</th>
                            @foreach($roles as $role)
                                <th class="text-center text-uppercase">{{ $role->name }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Loop through the GROUPS first --}}
                        @foreach($groupedPermissions as $groupName => $permissions)
                            
                            {{-- The Group Header Row --}}
                            <tr class="table-secondary">
                                <td colspan="{{ count($roles) + 1 }}" class="fw-bold text-dark">
                                    <i class="fe fe-folder me-1"></i> {{ $groupName }}
                                </td>
                            </tr>

                            {{-- Loop through specific Permissions inside the group --}}
                            @foreach($permissions as $permission)
                                <tr>
                                    <td class="ps-4">
                                        {{-- Format name: "book_student_lesson" -> "Book Student Lesson" --}}
                                        {{ ucwords(str_replace('_', ' ', $permission)) }}
                                    </td>
                                    
                                    @foreach($roles as $role)
                                        <td class="text-center">
                                            <div class="form-check form-switch d-flex justify-content-center">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       style="cursor: pointer;"
                                                       wire:click="togglePermission({{ $role->id }}, '{{ $permission }}')"
                                                       {{ $role->hasPermissionTo($permission) ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>