@extends('layouts.base')

@section('content')
    <!-- Подключаю стили прямо в шаблоне на случай, если layout не включает их -->
    <link rel="stylesheet" href="{{ asset('css/minimal.css') }}">

    <div class="container">
      <header class="header">
        <a href="{{ url('/') }}" class="brand">
          <span class="logo">EC</span>
          <div>
            <div class="kicker">Minimal shop</div>
            <div class="title">商品一覧</div>
          </div>
        </a>
        <nav>
          <a href="{{ route('cart.index') }}" class="nav-link">Cart</a>
          <a href="{{ url('/checkout/shipping') }}" class="nav-link">Checkout</a>
        </nav>
      </header>

      <section class="products-grid">
        @foreach ($products as $product)
          <article class="card">
            <a href="{{ route('products.show', ['id' => $product->id]) }}">
              <div class="thumb">
                <img src="{{ $product->image_url ?? asset('images/default-product.png') }}" alt="{{ $product->name }}">
              </div>
            </a>

            <div class="title">{{ $product->name }}</div>
            <div class="desc">{{ \Illuminate\Support\Str::limit($product->description ?? '', 100) }}</div>

            <div class="meta">
              <div class="price">¥{{ number_format($product->price) }}</div>
              <div>
                <a class="btn btn-primary" href="{{ route('products.show', ['id' => $product->id]) }}">View</a>
              </div>
            </div>
          </article>
        @endforeach
      </section>

      <h2 class="kicker" style="margin-top:1.5rem;">セール中の商品</h2>
      <section class="products-grid">
        @foreach ($saleProducts as $product)
          <article class="card">
            <a href="{{ route('products.show', ['id' => $product->id]) }}">
              <div class="thumb">
                <img src="{{ $product->image_url ?? asset('images/default-product.png') }}" alt="{{ $product->name }}">
              </div>
            </a>

            <div class="title">{{ $product->name }}</div>
            <div class="meta">
              <div class="price">¥{{ number_format($product->price) }}</div>
              <div><span class="badge">SALE</span></div>
            </div>
          </article>
        @endforeach
      </section>
    </div>
@endsection
