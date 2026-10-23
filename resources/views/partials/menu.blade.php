<div id="sidebar" class="c-sidebar c-sidebar-fixed c-sidebar-lg-show">

    <div class="c-sidebar-brand d-md-down-none">
        <a class="c-sidebar-brand-full h4" href="#">
            {{ trans('panel.site_title') }}
        </a>
    </div>

    <ul class="c-sidebar-nav">
        @can('dashboard_access')
        <li class="c-sidebar-nav-item">
            <a href="{{ route("admin.dashboard") }}" class="c-sidebar-nav-link">
                <i class="c-sidebar-nav-icon fas fa-fw fa-tachometer-alt">

                </i>
                {{ trans('global.dashboard') }}
            </a>
        </li>
        @endcan
        @can('user_management_access')
            <li class="c-sidebar-nav-dropdown {{ request()->is("admin/permissions*") ? "c-show" : "" }} {{ request()->is("admin/roles*") ? "c-show" : "" }} {{ request()->is("admin/users*") ? "c-show" : "" }}  {{ request()->is("admin/referrals*") ? "c-show" : "" }}">
                <a class="c-sidebar-nav-dropdown-toggle" href="#">
                    <i class="fa-fw fas fa-users c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.userManagement.title') }}
                </a>
                <ul class="c-sidebar-nav-dropdown-items">
                    @can('permission_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.permissions.index") }}"
                               class="c-sidebar-nav-link {{ request()->is("admin/permissions") || request()->is("admin/permissions/*") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-unlock-alt c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.permission.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('role_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.roles.index") }}"
                               class="c-sidebar-nav-link {{ request()->is("admin/roles") || request()->is("admin/roles/*") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-briefcase c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.role.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('user_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.users.index") }}"
                               class="c-sidebar-nav-link {{ request()->is("admin/users") || request()->is("admin/users/*") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-user c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.user.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('extra_admin_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.extra_admins.index") }}"
                               class="c-sidebar-nav-link {{  request()->is("admin/extra_admins/*") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-user c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.extraAdmin.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('worker_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.workers.index") }}"
                               class="c-sidebar-nav-link {{  request()->is("admin/workers/*") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-user c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.worker.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('referral_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.referrals.index") }}"
                               class="c-sidebar-nav-link {{  request()->is("admin/referrals/*") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-user c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.referral.title') }}
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcan
        @can('setting_access')
            <li class="c-sidebar-nav-dropdown {{ request()->is("admin/regions*") ? "c-show" : "" }} {{ request()->is("admin/cities*") || request()->is("admin/stepPages*")|| request()->is("admin/prepareText*") ? "c-show" : "" }}">
                <a class="c-sidebar-nav-dropdown-toggle" href="#">
                    <i class="fa-fw fas fa-cogs c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.setting.title') }}
                </a>
                <ul class="c-sidebar-nav-dropdown-items">
                    @can('region_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.regions.index") }}"
                               class="c-sidebar-nav-link {{ request()->is("admin/regions") || request()->is("admin/regions/*") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-globe-americas c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.region.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('city_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.cities.index") }}"
                               class="c-sidebar-nav-link {{ request()->is("admin/cities") || request()->is("admin/cities/*") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-map-marker-alt c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.city.title') }}
                            </a>
                        </li>
                    @endcan

                    @can('information_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.information.index") }}"
                               class="c-sidebar-nav-link {{ request()->is("admin/information") || request()->is("admin/information/*") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-info-circle c-sidebar-nav-icon">

                                </i>

                                {{ trans('cruds.information.title') }}
                            </a>
                        </li>
                    @endcan

                    @can('information_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.prepareText.index") }}"
                               class="c-sidebar-nav-link {{ request()->is("admin/prepareText") || request()->is("admin/prepareText/*") ? "c-active" : "" }}">

                                <i class="fa-fw fas fa-font c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.prepareText.title') }}
                            </a>
                        </li>
                    @endcan

                    @can('information_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.writingPrompt.index") }}"
                               class="c-sidebar-nav-link {{ request()->is("admin/writingPrompt") || request()->is("admin/writingPrompt/*") ? "c-active" : "" }}">

                                <i class="fa-fw fas fa-pen   c-sidebar-nav-icon"></i>

                                {{ trans('cruds.writingPrompt.title') }}
                            </a>
                        </li>
                    @endcan

                    @can('information_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.globalSettings.index") }}"
                               class="c-sidebar-nav-link {{ request()->is("admin/globalSettings") || request()->is("admin/globalSettings/*") ? "c-active" : "" }}">

                                <i class="fa-fw fas fa-pen   c-sidebar-nav-icon"></i>

                                {{ trans('cruds.globalSetting.title') }}
                            </a>
                        </li>
                    @endcan

                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.stepPages.index") }}"
                               class="c-sidebar-nav-link {{ request()->is("admin/stepPages*") ? "c-active" : "" }}">
                                <i class="fa-fw  fas fa-file   c-sidebar-nav-icon"></i>
                                <i class=" fa-rectangle-list"></i>
                                {{ trans('cruds.stepPage.title') }}
                            </a>
                        </li>
                </ul>
            </li>
        @endcan
        <li class="c-sidebar-nav-dropdown {{ request()->is("admin/reports*") ? "c-show" : "" }}">
            <a class="c-sidebar-nav-dropdown-toggle" href="#">
                <i class="fa-fw fas fa-chart-bar c-sidebar-nav-icon"></i>
                Hisobotlar
            </a>
            <ul class="c-sidebar-nav-dropdown-items">
                <li class="c-sidebar-nav-item">
                    <a href="{{ route("admin.reports.daily") }}"
                       class="c-sidebar-nav-link {{ request()->is("admin/reports/daily") ? "c-active" : "" }}">
                        <i class="fa-fw fas fa-clipboard-list c-sidebar-nav-icon"></i>
                        Kunlik hisobotlar
                    </a>
                </li>
                <li class="c-sidebar-nav-item">
                    <a href="{{ route("admin.reports.monthly") }}"
                       class="c-sidebar-nav-link {{ request()->is("admin/reports/monthly") ? "c-active" : "" }}">
                        <i class="fa-fw fas fa-calendar-alt c-sidebar-nav-icon"></i>
                        Oylik hisobotlar
                    </a>
                </li>
                <li class="c-sidebar-nav-item">
                    <a href="{{ route("admin.reports.overview") }}"
                       class="c-sidebar-nav-link {{ request()->is("admin/reports/overview") ? "c-active" : "" }}">
                        <i class="fa-fw fas fa-chart-pie c-sidebar-nav-icon"></i>
                        Umumiy hisobotlar
                    </a>
                </li>
            </ul>
        </li>
        @can('premium_access')
            <li class="c-sidebar-nav-dropdown {{ request()->is("admin/plans*") ? "c-show" : "" }} {{ ( request()->is("admin/facilities*") ||  request()->is("admin/package_users*") ) ? "c-show" : "" }}">
                <a class="c-sidebar-nav-dropdown-toggle" href="#">
                    <i class="fa-fw fas  fa-crown c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.premium.title') }}
                </a>
                <ul class="c-sidebar-nav-dropdown-items">
                    @can('plan_access')

                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.plans.index") }}"
                               class="c-sidebar-nav-link {{ request()->is("admin/plans")  ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-plane-departure c-sidebar-nav-icon">
                                </i>
                                {{ trans('cruds.premium.fields.plans') }}
                            </a>
                        </li>
                    @endcan
                    @can('plan_access')

                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.packages.index") }}"
                               class="c-sidebar-nav-link {{ request()->is("admin/packages")  ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-box c-sidebar-nav-icon"></i>
                                </i>
                                {{ trans('cruds.premium.fields.packages') }}
                            </a>
                        </li>
                    @endcan
                    @can('facility_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.facilities.index") }}"
                               class="c-sidebar-nav-link {{ request()->is("admin/facilities")  ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-feather c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.premium.fields.facilities') }}
                            </a>
                        </li>
                    @endcan
                    @can('premium_users_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.premium_users.index") }}"
                               class="c-sidebar-nav-link {{ request()->is("admin/premium_users*")  ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-user c-sidebar-nav-icon">
                                </i>
                                {{ trans('cruds.premium.fields.premium_users') }}
                            </a>
                        </li>
                    @endcan
                    @can('premium_users_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.package_users.index") }}"
                               class="c-sidebar-nav-link {{ request()->is("admin/package_users*")  ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-user-tag c-sidebar-nav-icon"></i>
                                {{ trans('cruds.premium.fields.package_users') }}
                            </a>
                        </li>
                    @endcan
                    @can('promoCode_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.promoCodes.index") }}"
                               class="c-sidebar-nav-link {{ request()->is("admin/promoCodes*")  ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-ticket-alt c-sidebar-nav-icon"></i>
                                {{ trans('cruds.premium.fields.promo_codes') }}
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcan
        @can('course_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.courses.index") }}"
                   class="c-sidebar-nav-link {{ request()->is("admin/courses") || request()->is("admin/courses/*") || request()->is("admin/lessons*") || request()->is("admin/lessons/index2") || request()->is("admin/lessons*") ? "c-active" : "" }}">
                      <i class="fa-fw fas fa-clipboard-list c-sidebar-nav-icon">
                    </i>
                    {{ trans('cruds.course.title') }}
                </a>
            </li>
        @endcan
        @can('listening_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.listening.exercises.index") }}"
                   class="c-sidebar-nav-link {{ request()->is("admin/courses") || request()->is("admin/courses/*") || request()->is("admin/lessons*") || request()->is("admin/lessons/index2") || request()->is("admin/lessons*") ? "c-active" : "" }}">
                    <i class="fa-fw fas fa-headphones c-sidebar-nav-icon">
                    </i>


                    {{ trans('cruds.listening.title') }}
                </a>
            </li>
        @endcan
        @can('reading_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.reading.exercises.index") }}"
                   class="c-sidebar-nav-link {{ request()->is("admin/reading*") ? "c-active" : "" }}">
                    <i class="fa-fw fas fa-book-open c-sidebar-nav-icon"></i>

                    {{ trans('cruds.reading.title') }}
                </a>
            </li>
        @endcan
        @can('writing_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.writing.exercises.index") }}"
                   class="c-sidebar-nav-link {{ request()->is("admin/writing*") ? "c-active" : "" }}">
                    <i class="fa-fw fas fa-pen   c-sidebar-nav-icon"></i>

                    {{ trans('cruds.writing.title') }}
                </a>
            </li>
        @endcan
        @can('speaking_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.speaking.exercises.index") }}"
                   class="c-sidebar-nav-link {{ request()->is("admin/speaking*") ? "c-active" : "" }}">
                    <i class="fa-fw fas fa-music   c-sidebar-nav-icon"></i>

                    {{ trans('cruds.speaking.title') }}
                </a>
            </li>
        @endcan
        @can('full_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.full.exercises.index") }}"
                   class="c-sidebar-nav-link {{ request()->is("admin/full*") ? "c-active" : "" }}">
                    <i class="fa-fw  fas fa-list   c-sidebar-nav-icon"></i>
                    <i class=" fa-rectangle-list"></i>
                    {{ trans('cruds.exercise.full') }}
                </a>
            </li>
        @endcan
        @can('comment_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.comments.index") }}"
                   class="c-sidebar-nav-link {{ request()->is("admin/full*") ? "c-active" : "" }}">
                    <i class="fa-fw  fas fa-comment   c-sidebar-nav-icon"></i>
                    <i class=" fa-rectangle-list"></i>
                    {{ trans('cruds.comment.title') }}
                </a>
            </li>
        @endcan



        @can('news_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.newss.index") }}"
                   class="c-sidebar-nav-link {{ request()->is("admin/newss") || request()->is("admin/newss/*") ? "c-active" : "" }}">
                    <i class="fa-fw far fa-newspaper c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.news.title') }}
                </a>
            </li>
        @endcan
        @can('application_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.applications.index") }}"
                   class="c-sidebar-nav-link {{ request()->is("admin/applications") || request()->is("admin/applications/*") ? "c-active" : "" }}">
                    <i class="fa-fw fas fa-users c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.application.title') }}
                </a>
            </li>
        @endcan
        @can('sms_access')
            <li class="c-sidebar-nav-dropdown {{ request()->is("admin/shablons*") ? "c-show" : "" }} {{ request()->is("admin/messages*") ? "c-show" : "" }}">
                <a class="c-sidebar-nav-dropdown-toggle" href="#">
                    <i class="fa-fw fas fa-comments c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.sms.title') }}
                </a>
                <ul class="c-sidebar-nav-dropdown-items">
                    @can('shablon_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.shablons.index") }}"
                               class="c-sidebar-nav-link {{ request()->is("admin/shablons") || request()->is("admin/shablons/*") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-envelope-open c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.shablon.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('message_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.messages.index") }}"
                               class="c-sidebar-nav-link {{ request()->is("admin/messages") || request()->is("admin/messages/*") ? "c-active" : "" }}">
                                <i class="fa-fw far fa-envelope c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.message.title') }}
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcan

{{--        @can('sms_access')--}}
{{--            <li class="c-sidebar-nav-item">--}}
{{--                <a href="{{ route("payment.dashboard") }}"--}}
{{--                   class="c-sidebar-nav-link">--}}
{{--                    <i class="fa-fw fas fa-money c-sidebar-nav-icon">--}}

{{--                    </i>--}}
{{--                   Billing Config--}}
{{--                </a>--}}
{{--            </li>--}}

{{--            @endcan--}}
        {{--        @can('comment_access')--}}
        {{--            <li class="c-sidebar-nav-item">--}}
        {{--                <a href="{{ route("admin.comments.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/comments") || request()->is("admin/comments/*") ? "c-active" : "" }}">--}}
        {{--                    <i class="fa-fw fas fa-cogs c-sidebar-nav-icon">--}}

        {{--                    </i>--}}
        {{--                    {{ trans('cruds.comment.title') }}--}}
        {{--                </a>--}}
        {{--            </li>--}}
        {{--        @endcan--}}
        {{--        @can('saved_lesson_access')--}}
        {{--            <li class="c-sidebar-nav-item">--}}
        {{--                <a href="{{ route("admin.saved-lessons.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/saved-lessons") || request()->is("admin/saved-lessons/*") ? "c-active" : "" }}">--}}
        {{--                    <i class="fa-fw fas fa-cogs c-sidebar-nav-icon">--}}

        {{--                    </i>--}}
        {{--                    {{ trans('cruds.savedLesson.title') }}--}}
        {{--                </a>--}}
        {{--            </li>--}}
        {{--        @endcan--}}
        @if(file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php')))
            @can('profile_password_edit')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{ request()->is('profile/password') || request()->is('profile/password/*') ? 'c-active' : '' }}"
                       href="{{ route('profile.password.edit') }}">
                        <i class="fa-fw fas fa-key c-sidebar-nav-icon">
                        </i>
                        {{ trans('global.change_password') }}
                    </a>
                </li>
            @endcan
        @endif
        <li class="c-sidebar-nav-item">
            <a href="#" class="c-sidebar-nav-link"
               onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                <i class="c-sidebar-nav-icon fas fa-fw fa-sign-out-alt">

                </i>
                {{ trans('global.logout') }}
            </a>
        </li>
    </ul>

</div>
