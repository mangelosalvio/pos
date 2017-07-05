@extends('auth.auth')
@section('content')
    <body class="fullbleed vertical layout">
    <div class="flex-2"></div>
    <div class="vertical layout center flex-12">
        <paper-card style="width:35vw;" heading="Apollo Restaurant">
            <div class="card-content">
                {!! Form::open(['url' => '/auth/register']) !!}
                    <paper-input label="Name" name="name" id="name" value="{{ old('name') }}" autofocus>
                        <div prefix>
                            <iron-icon icon="social:person" style="color:#e0e0e0;"></iron-icon>
                        </div>
                    </paper-input>

                    <paper-input label="Username" name="username" id="username" value="{{ old('username') }}" autofocus>
                        <div prefix>
                            <iron-icon icon="social:person" style="color:#e0e0e0;"></iron-icon>
                        </div>
                    </paper-input>

                    <paper-input label="Email" name="email" id="email" value="{{ old('email') }}" autofocus>
                        <div prefix>
                            <iron-icon icon="mail" style="color:#e0e0e0;"></iron-icon>
                        </div>
                    </paper-input>

                    <paper-input label="Password" name="password" id="password" type="password">
                        <div prefix>
                            <iron-icon icon="https" style="color:#e0e0e0;"></iron-icon>
                        </div>
                    </paper-input>

                    <paper-input label="Confirm Password" name="password_confirmation" id="password_confirmation" type="password">
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
                            <button>Register</button>
                        </paper-button>
                    </div>
                {!! Form::close() !!}
            </div>
        </paper-card>
    </div>
    <div class="flex"></div>
    </body>
@endsection