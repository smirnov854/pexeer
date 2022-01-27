<div class="row">
    <div class="col-xl-4 mb-xl-0 mb-4">
        <div class="card cp-user-custom-card">
            <div class="card-body">
                <div class="user-profile-area">
                    <div class="user-profile-img">
                        <img src="{{show_image($user->id,'user')}}" class="img-fluid" alt="">
                    </div>
                    <form enctype="multipart/form-data" method="post"
                          action="{{route('uploadProfileImage')}}">
                        @csrf
                        <div class="user-cp-user-profile-info">
                            <input type="file" name="file_one" id="upload-user-img">
                            <label for="upload-user-img" class="upload-user-img">
                                {{__('Upload New Image')}}
                            </label>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-8">
        <div class="card cp-user-custom-card">
            <div class="card-body">
                <div class="cp-user-profile-header">
                    <h5>{{__('Edit Profile Information')}}</h5>
                </div>
                <div class="cp-user-profile-info">
                    <form action="{{route('userProfileUpdate')}}" method="post">
                        @csrf
                        <div class="form-group">
                            <label>{{__('First Name')}}</label>
                            <input type="text" name="first_name" value="{{$user->first_name}}"
                                   class="form-control">
                        </div>
                        <div class="form-group">
                            <label>{{__('Last Name')}}</label>
                            <input type="text" name="last_name" value="{{$user->last_name}}"
                                   class="form-control">
                        </div>
                        <div class="form-group">
                            <label>{{__('Phone')}}</label>
                            <input type="text" name="phone" value="{{$user->phone}}"
                                   class="form-control">
                        </div>
                        <div class="form-group">
                            <label>{{__('Country')}}</label>
                            <select name="country" id="" class="form-control">
                                @foreach(countrylist() as $key => $value)
                                    <option value="{{$key}}"
                                            @if(isset($user->country) && ($user->country == $key)) selected @endif>
                                        {{$value}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>{{__('Gender')}}</label>
                            <select class="form-control" name="gender" id="">
                                <option @if($user->gender == 1) selected
                                        @endif value="1">{{__('Male')}}</option>
                                <option @if($user->gender == 2) selected
                                        @endif value="2">{{__('Female')}}</option>
                                <option @if($user->gender == 3) selected
                                        @endif value="3">{{__('Others')}}</option>
                            </select>
                        </div>
                        <div class="form-group m-0">
                            <button class="btn profile-edit-btn"
                                    type="submit">{{__('Update')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
