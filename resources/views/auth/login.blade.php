@extends('auth.auth')
@section('content')
    <body class="fullbleed vertical layout">
        <div class="flex"></div>
        <div class="vertical layout center flex">
            <paper-card style="width:35vw;" heading="{{ Config::get('constants.COMPANY_NAME') }}">
                <div class="card-content">
                    <form method="POST" action="/auth/login">
                        {!! csrf_field() !!}

                        <paper-input label="Username" name="username" id="username" value="{{ old('username') }}" autofocus>
                            <div prefix>
                                <iron-icon icon="social:person" style="color:#e0e0e0;"></iron-icon>
                            </div>
                        </paper-input>
                        <paper-input label="Password" name="password" id="password" type="password">
                            <div prefix>
                                <iron-icon icon="https" style="color:#e0e0e0;"></iron-icon>
                            </div>
                        </paper-input>

                        @if (count($errors) > 0)
                            <div class="alert alert-danger" style="padding:10px; padding-left: 20px;">
                                @foreach ($errors->all() as $error)
                                    <div style="color:#c53929;">{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif


                        <div>
                            <paper-button raised class="flex">
                                <button>Log-in</button>
                            </paper-button>
                        </div>
                    </form>
                </div>
            </paper-card>
        </div>
        <div class="flex"></div>
    </body>
@endsection