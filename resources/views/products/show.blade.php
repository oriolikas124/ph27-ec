@extends('layouts.base')

@section('content')
    <!-- Подключаю стили прямо в шаблоне на случай, если layout не включает их -->
    <link rel="stylesheet" href="{{ asset('css/minimal.css') }}">

    <div class="container">
      <div class="product-page">
        <div class="product-details">
          <div class="product-hero">
            <img src="{{ $product->image_url ?? asset('images/default-product.png') }}" alt="{{ $product->name }}" style="width:100%; height:auto; display:block;">
          </div>

          <h2 class="title" style="margin-top:1rem;">{{ $product->name }}</h2>
          <div class="kicker">{{ $product->category->name ?? '' }}</div>
          <p class="desc" style="margin-top:.5rem;">{{ $product->description }}</p>
        </div>

        <aside class="product-sidebar">
          <div class="form">
            <div class="price">¥{{ number_format($product->price) }}</div>

            <form action="{{ route('cart.store') }}" method="post">
                @csrf

                @error('quantity')
                    <p class="kicker" style="color:#d14343">{{ $message }}</p>
                @enderror

                <input type="hidden" name="productId" value="{{ $product->id }}">

                <label for="quantity">数量</label>
                <input id="quantity" type="number" name="quantity" value="1" min="1" class="@error('quantity') input-error @enderror" style="margin-bottom:.5rem;">

                <div class="actions">
                  <button class="btn btn-primary" type="submit">カートに入れる</button>
                  <a class="btn btn-ghost" href="{{ url()->previous() }}">戻る</a>
                </div>
            </form>
          </div>
        </aside>
      </div>
    </div>
@endsection
