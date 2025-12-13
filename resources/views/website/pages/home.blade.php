@extends('website.layouts.app')

@section('content')
    @include('website.components.website.hero')

    @include('website.components.website.sobre')

    @include('website.components.website.projetos')

    @include('website.components.website.estatisticas')

    @include('website.components.website.contato')
@endsection
