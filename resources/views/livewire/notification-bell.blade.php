<div>
    <a class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill"
       href="javascript:void(0);"
       data-bs-toggle="dropdown"
       data-bs-auto-close="outside"
       aria-expanded="false">
        <i class="icon-base ri ri-notification-2-line icon-22px"></i>
        @if($unreadCount > 0)
            <span class="position-absolute top-0 start-50 translate-middle-y badge badge-dot bg-danger mt-2 border"></span>
        @endif
    </a>
    <ul class="dropdown-menu dropdown-menu-end py-0">
        <li class="dropdown-menu-header border-bottom py-50">
            <div class="dropdown-header d-flex align-items-center py-2">
                <h6 class="mb-0 me-auto">{{ __('messages.notifications') }}</h6>
                <div class="d-flex align-items-center h6 mb-0">
                    @if($unreadCount > 0)
                        <span class="badge rounded-pill bg-label-primary fs-xsmall me-2">{{ $unreadCount }} Nueva{{ $unreadCount > 1 ? 's' : '' }}</span>
                    @endif
                    <a href="javascript:void(0)"
                       wire:click="markAllAsRead"
                       class="dropdown-notifications-all p-2"
                       data-bs-toggle="tooltip"
                       data-bs-placement="top"
                       title="{{ __('messages.mark_all_read') }}">
                        <i class="icon-base ri ri-mail-open-line text-heading"></i>
                    </a>
                </div>
            </div>
        </li>
        <li class="dropdown-notifications-list scrollable-container">
            <ul class="list-group list-group-flush">
                @forelse($notifications as $notification)
                    <li class="list-group-item list-group-item-action dropdown-notifications-item {{ $notification->read_at ? 'marked-as-read' : '' }}"
                        wire:click="markAsRead({{ $notification->id }})">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar">
                                    <span class="avatar-initial rounded-circle bg-label-{{ $notification->type === 'success' ? 'success' : ($notification->type === 'warning' ? 'warning' : ($notification->type === 'error' ? 'danger' : 'primary')) }}">
                                        <i class="icon-base ri ri-{{ $notification->type === 'success' ? 'check' : ($notification->type === 'warning' ? 'alert' : ($notification->type === 'error' ? 'close' : 'information')) }}-line icon-18px"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="small mb-1">{{ $notification->title }}</h6>
                                <small class="mb-1 d-block text-body">{{ $notification->message }}</small>
                                <small class="text-body-secondary">{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                            <div class="flex-shrink-0 dropdown-notifications-actions">
                                @if(!$notification->read_at)
                                    <a href="javascript:void(0)" class="dropdown-notifications-read">
                                        <span class="badge badge-dot"></span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="list-group-item text-center py-4">
                        <i class="icon-base ri ri-notification-off-line icon-48px text-muted mb-2"></i>
                        <p class="text-muted mb-0">{{ __('messages.no_notifications') }}</p>
                    </li>
                @endforelse
            </ul>
        </li>
        @if($notifications->count() > 0)
            <li class="border-top">
                <div class="d-grid p-4">
                    <a class="btn btn-primary btn-sm d-flex" href="javascript:void(0);">
                        <small class="align-middle">{{ __('messages.view_all_notifications') }}</small>
                    </a>
                </div>
            </li>
        @endif
    </ul>
</div>
