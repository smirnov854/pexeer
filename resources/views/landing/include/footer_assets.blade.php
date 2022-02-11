<!-- Js file  -->
<script src="{{asset('assets/landing/custom/assets/js/jquery-3.6.0.min.js')}}"></script>
<script src="{{asset('assets/landing/custom/assets/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('assets/landing/custom/assets/js/plugins.js')}}"></script>
<script src="{{asset('assets/landing/custom/assets/js/main.js')}}"></script>
<script src="{{asset('assets/common/sweetalert/sweetalert.js')}}"></script>
<script>
    (function($) {
        "use strict";

        @if(session()->has('success'))
        swal({
            text: '{{ session('success') }}',
            icon: "success",
            buttons: false,
            timer: 3000,
        });

        @elseif(session()->has('dismiss'))
        swal({
            text: '{{ session('dismiss') }}',
            icon: "warning",
            buttons: false,
            timer: 3000,
        });

        @elseif($errors->any())
        @foreach($errors->getMessages() as $error)
        swal({
            text: '{{ $error[0] }}',
            icon: "error",
            buttons: false,
            timer: 3000,
        });
        @break
        @endforeach
        @endif

    })(jQuery);

</script>
