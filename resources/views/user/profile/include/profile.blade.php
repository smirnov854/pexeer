<div class="row">
    <div class="col-xl-4 mb-xl-0 mb-4">
        <div class="card cp-user-custom-card">
            <div class="card-body">
                <div class="user-profile-area">
                    <div class="user-profile-img">
                        <img src="{{show_image($user->id,'user')}}" class="img-fluid" alt="">
                    </div>
                    <div class="user-cp-user-profile-info">
                        <h4>{{$user->first_name.' '.$user->last_name}}</h4>
                        <p>{{$user->email}}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-8">
        <div class="card cp-user-custom-card">
            <div class="card-body">
                <div class="cp-user-profile-header">
                    <h5>{{__('Profile Information')}}</h5>
                </div>
                <div class="cp-user-profile-info">
                    <ul>
                        <li>
                            <span>{{__('Name')}}</span>
                            <span class="cp-user-dot">:</span>
                            <span>{{$user->first_name.' '.$user->last_name}}</span>
                        </li>
                        <li>
                            <span>{{__('Country')}}</span>
                            <span class="cp-user-dot">:</span>
                            <span>
                                @if(!empty($user->country))
                                    {{countrylist(strtoupper($user->country))}}
                                @endif
                            </span>
                        </li>
                        <li>
                            <span>{{__('Email')}}</span>
                            <span class="cp-user-dot">:</span>
                            <span>{{$user->email}}</span>
                        </li>
                        <li>
                            <span>{{__('Email Verification')}}</span>
                            <span class="cp-user-dot">:</span>
                            <span>{{statusAction($user->is_verified)}}</span>
                        </li>
                        <li>
                            <span>{{__('Phone')}}</span>
                            <span class="cp-user-dot">:</span>
                            <span>{{$user->phone}}</span>
                        </li>
                        <li>
                            <span>{{__('Role')}}</span>
                            <span class="cp-user-dot">:</span>
                            <span>{{userRole($user->role)}}</span>
                        </li>
                        <li>
                            <span>{{__('Active Status')}}</span>
                            <span class="cp-user-dot">:</span>
                            <span>{{statusAction($user->status)}}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
