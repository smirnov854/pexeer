@extends('admin.master',['menu'=>'profile'])
@section('title', isset($title) ? $title : '')
@section('style')
@endsection
@section('content')
    <!-- breadcrumb -->
    <div class="custom-breadcrumb">
        <div class="row">
            <div class="col-12">
                <ul>
                    <li class="active-item">{{__('Profile')}}</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /breadcrumb -->

    <!-- User Management -->
    <div class="user-management profile">
        <div class="row">
            <div class="col-12">
                <ul class="nav user-management-nav profile-nav mb-3 profile-tab-b" id="pills-tab" role="tablist">
                    <li>
                        <a class=" @if(isset($tab) && $tab=='profile') active @endif nav-link " data-id="profile" data-toggle="pill" role="tab" data-controls="profile" aria-selected="true" href="#profile">
                            <img src="{{asset('assets/admin/images/user-management-icons/user.svg')}}" class="img-fluid" alt="">
                            <span>{{__('My Profile')}}</span>
                        </a>
                    </li>
                    <li>
                        <a class=" @if(isset($tab) && $tab=='edit_profile') active @endif nav-link  " data-id="edit_profile" data-toggle="pill" role="tab" data-controls="edit_profile" aria-selected="true" href="#edit_profile">
                            <img src="{{asset('assets/admin/images/edit-profile.svg')}}" class="img-fluid" alt="">
                            <span>{{__('Edit Profile')}}</span>
                        </a>
                    </li>
                    <li>
                        <a class=" @if(isset($tab) && $tab=='change_pass') active @endif nav-link  " data-id="change_pass" data-toggle="pill" role="tab" data-controls="change_pass" aria-selected="true" href="#change_pass">
                            <img src="{{asset('assets/admin/images/reset-pass.svg')}}" class="img-fluid" alt="">
                            <span>{{__('Change Password')}}</span>
                        </a>
                    </li>
                </ul>
                <div class="tab-content tab-pt-n" id="tabContent">
                    <!-- genarel-setting start-->
                    <div class="tab-pane fade show @if(isset($tab) && $tab=='profile')  active @endif " id="profile" role="tabpanel" aria-labelledby="general-setting-tab">
                        <div class="form-area plr-65">
                            <h4 class="mb-4">{{__('My Profile')}}</h4>
                            <div class="row">
                                <div class="col-lg-5">
                                    <div class="profile-img-area pro-img text-center">
                                        <div class="prifile-img">
                                            <img width="100" src="{{show_image(Auth::user()->id,'user')}}" alt="profile">
                                        </div>
                                        <div class="profile-name">
                                            <h3>{!! clean(Auth::user()->first_name.' '.Auth::user()->last_name) !!}</h3>
                                            <span>{!! clean(Auth::user()->email) !!}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 offset-lg-1">
                                    <div class="profile-info">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <tbody>
                                                <tr>
                                                    <td>{{__('Name')}}</td>
                                                    <td>:</td>
                                                    <td><span>{!! clean(Auth::user()->first_name.' '.Auth::user()->last_name) !!}</span></td>
                                                </tr>
                                                <tr>
                                                    <td>{{__('Role')}}</td>
                                                    <td>:</td>
                                                    <td><span>{{userRole($user->role)}}</span></td>
                                                </tr>
                                                <tr>
                                                    <td>{{__('Email')}}</td>
                                                    <td>:</td>
                                                    <td><span>{!! clean(Auth::user()->email) !!}</span></td>
                                                </tr>
                                                <tr>
                                                    <td>{{__('Email verification')}}</td>
                                                    <td>:</td>
                                                    <td><span class="color">{{statusAction($user->is_verified)}}</span></td>
                                                </tr>
                                                <tr>
                                                    <td>{{__('Contact')}}</td>
                                                    <td>:</td>
                                                    <td><span>{{\Illuminate\Support\Facades\Auth::user()->phone}}</span></td>
                                                </tr>
                                                <tr>
                                                    <td>{{__('Status')}}</td>
                                                    <td>:</td>
                                                    <td><span>{{statusAction($user->status)}}</span></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade @if(isset($tab) && $tab=='edit_profile')show active @endif" id="edit_profile" role="tabpanel" aria-labelledby="apisetting-tab">
                        <div class="form-area">
                            <h4 class="mb-4">{{__('Edit Profile')}}</h4>
                            <div class="row">
                                <div class="col-lg-5">
                                    <div class="profile-img-area text-center">
                                        <div class="uplode-img">
                                            <form enctype="multipart/form-data" method="post" action="{{route('uploadProfileImage')}}">
                                                @csrf
                                                <div id="file-upload" class="section-p">
                                                    <input type="file" name="file_one" value="" id="file" ref="file" class="dropify" data-default-file="{{show_image(Auth::user()->id,'user')}}" />
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-7">
                                    <div class="profile-info-form form-area  p-0">
                                        <form action="{{route('UserProfileUpdate')}}" method="post">
                                            @csrf
                                            <div class="form-group">
                                                <label for="firstname">{{__('First Name')}}</label>
                                                <input name="first_name" value="{{old('first_name',Auth::user()->first_name)}}" type="text" class="form-control" id="firstname" placeholder="{{__('First name')}}">
                                                @error('first_name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label for="lastname">{{__('Last Name')}}</label>
                                                <input name="last_name" value="{{old('last_name',Auth::user()->last_name)}}" type="text" class="form-control" id="lastname" placeholder="{{__('Last name')}}">
                                                @error('last_name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label for="email">{{__('Phone Number')}}</label>
                                                <input name="phone"   type="text" value="{{old('phone',Auth::user()->phone)}}" class="form-control" id="phoneVerify" placeholder="{{__('01999999999')}}">
                                                @error('phone')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label for="email">{{__('Email')}}</label>
                                                <input name="" readonly type="email" value="{{old('email',Auth::user()->email)}}" class="form-control" id="email" placeholder="{{__('email')}}">
                                                @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                            <button type="submit" class="btn">{{__('Update')}}</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade @if(isset($tab) && $tab=='change_pass')show active @endif" id="change_pass" role="tabpanel" aria-labelledby="braintree-tab">
                        <div class="form-area ">
                            <h4 class="mb-4">{{__('Change Password')}}</h4>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="profile-info-form form-area p-0">
                                        <form method="POST" action="{{route('changePasswordSave')}}">
                                            @csrf
                                            <div class="form-group">
                                                <label for="currentpassword">{{__('Current Password')}}</label>
                                                <input name="password" type="password" class="form-control" id="currentpassword" placeholder="">
                                                <span class="flaticon-look"></span>
                                            </div>
                                            <div class="form-group">
                                                <label for="newpassword">{{__('New Password')}}</label>
                                                <input name="new_password" type="password" class="form-control" id="newpassword" placeholder="">
                                                <span class="flaticon-look"></span>
                                            </div>
                                            <div class="form-group">
                                                <label for="confirmpassword">{{__('Confirm Password')}}</label>
                                                <input name="confirm_new_password" type="password" class="form-control" id="confirmpassword" placeholder="">
                                                <span class="flaticon-look"></span>
                                            </div>
                                            <button type="submit" class="btn">{{__('Change Password')}}</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /User Management -->

@endsection

@section('script')
    <script>
        $('.nav-link').on('click', function () {
            var query = $(this).data('id');
            window.history.pushState('page2', 'Title', '{{route('adminProfile')}}?tab=' + query);
            $('.nav-link').removeClass('active');
            $(this).addClass('active');
            var str = '#' + $(this).data('controls');
            $('.tab-pane').removeClass('show active');
            $(str).addClass('show active');
        });

        jQuery("#file").on('change', function () {
            this.form.submit();

        });

        $(function () {
            $(document.body).on('submit', '.Upload', function (e) {
                e.preventDefault();
                $('.error_msg').addClass('d-none');
                $('.succ_msg').addClass('d-none');
                var form = $(this);
                $.ajax({
                    type: "POST",
                    enctype: 'multipart/form-data',
                    url: form.attr('action'),
                    data: new FormData($(this)[0]),
                    async: false,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (data) {
                        if (data.success == true) {
                            $('.succ_msg').removeClass('d-none');
                            $('.succ_msg').html(data.message);
                        } else {

                            $('.error_msg').removeClass('d-none');
                            $('.error_msg').html(data.message);

                        }
                    }
                });
                return false;
            });
        });

    </script>
@endsection
