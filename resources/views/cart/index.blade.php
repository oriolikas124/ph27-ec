@extends('layouts.base')

@section('content')
<link rel="stylesheet" href="{{ asset('css/minimal.css') }}">

<div class="container">
  <header class="header">
    <a href="{{ url('/') }}" class="brand">
      <span class="logo">EC</span>
      <div>
        <div class="kicker">Your cart</div>
        <div class="title">カート</div>
      </div>
    </a>
    <nav>
      <a href="{{ route('products.index') ?? url('/') }}" class="nav-link">Products</a>
      <a href="{{ route('checkout.shipping') }}" class="nav-link">Checkout</a>
    </nav>
  </header>

  <main class="form">
    @if(session('success'))
      <div class="kicker" style="color:green">{{ session('success') }}</div>
    @endif

    @if(empty($cart) || count($cart) === 0)
      <p class="kicker">カートに商品がありません。</p>
      <a class="btn btn-primary" href="{{ route('products.index') }}">買い物を続ける</a>
    @else
      <table style="width:100%; border-collapse:collapse;">
        <thead>
          <tr style="text-align:left; color:var(--muted); font-size:.95rem;">
            <th style="padding:.5rem 0">商品</th>
            <th style="padding:.5rem 0">価格</th>
            <th style="padding:.5rem 0">数量</th>
            <th style="padding:.5rem 0">小計</th>
            <th style="padding:.5rem 0"></th>
          </tr>
        </thead>
        <tbody>
          @foreach($cart as $item)
            <tr>
              <td style="padding:.6rem 0; vertical-align:middle;">
                <div style="display:flex; gap:.6rem; align-items:center;">
                  <img src="{{ $item['image_url'] ?? asset('images/default-product.png') }}" alt="{{ $item['name'] }}" style="width:56px; height:56px; object-fit:cover; border-radius:6px;">
                  <div>
                    <div style="font-weight:600;">{{ $item['name'] }}</div>
                  </div>
                </div>
              </td>
              <td style="padding:.6rem 0;">¥{{ number_format($item['price']) }}</td>
              <td style="padding:.6rem 0;">
                <form action="{{ route('cart.update') }}" method="post" style="display:flex; gap:.4rem; align-items:center;">
                  @csrf
                  <input type="hidden" name="productId" value="{{ $item['id'] }}">
                  <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="0" style="width:72px; padding:.4rem;">
                  <button class="btn btn-ghost" type="submit">更新</button>
                </form>
              </td>
              <td style="padding:.6rem 0;">¥{{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 0)) }}</td>
              <td style="padding:.6rem 0;">
                <form action="{{ route('cart.remove') }}" method="post">
                  @csrf
                  <input type="hidden" name="productId" value="{{ $item['id'] }}">
                  <button class="btn btn-ghost" type="submit">削除</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>

      <div style="display:flex; justify-content:flex-end; margin-top:1rem; gap:1rem; align-items:center;">
        <div class="review">
          <div class="line">
            <div class="kicker">合計</div>
            <div class="total">¥{{ number_format($total ?? 0) }}</div>
          </div>
        </div>

        <div style="display:flex; gap:.6rem;">
          <form action="{{ route('cart.clear') }}" method="post">
            @csrf
            <button class="btn btn-ghost" type="submit">カートを空にする</button>
          </form>

          <a class="btn btn-primary" href="{{ route('checkout.shipping') }}">配送先へ進む</a>
        </div>
      </div>
    @endif
  </main>
</div>
@endsection
