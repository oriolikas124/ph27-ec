<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();

        $saleProducts = Product::where('price', '<', 100)->get();

        $products = $products->map(function ($p) {
            if (!empty($p->image)) {

                if (preg_match('/^(https?:)?\/\//', $p->image) || str_starts_with($p->image, '/')) {
                    $p->image_url = $p->image;
                } else {
                    $p->image_url = asset('storage/' . $p->image);
                }
            } elseif (!empty($p->image_path)) {
                $p->image_url = asset('storage/' . $p->image_path);
            } else {
                $p->image_url = asset('images/default-product.png');
            }
            return $p;
        });

        $saleProducts = $saleProducts->map(function ($p) {
            if (!empty($p->image)) {
                if (preg_match('/^(https?:)?\/\//', $p->image) || str_starts_with($p->image, '/')) {
                    $p->image_url = $p->image;
                } else {
                    $p->image_url = asset('storage/' . $p->image);
                }
            } elseif (!empty($p->image_path)) {
                $p->image_url = asset('storage/' . $p->image_path);
            } else {
                $p->image_url = asset('images/default-product.png');
            }
            return $p;
        });

        return view('products.index', [
            'products' => $products,
            'saleProducts' => $saleProducts,
        ]);
    }

    public function show(int $id)
    {
        // ID が一致するデータを取得する
        // 存在しないIDが指定されたら404になる
        $product = Product::findOrFail($id);

        if (!empty($product->image)) {
            if (preg_match('/^(https?:)?\/\//', $product->image) || str_starts_with($product->image, '/')) {
                $product->image_url = $product->image;
            } else {
                $product->image_url = asset('storage/' . $product->image);
            }
        } elseif (!empty($product->image_path)) {
            $product->image_url = asset('storage/' . $product->image_path);
        } else {
            $product->image_url = asset('images/default-product.png');
        }

        return view('products.show', [
            'product' => $product,
        ]);
    }

    public function shippingForm()
    {
        $shipping = session('shipping', []);

        $countries = [
            'JP' => 'Japan',
            'US' => 'United States',
            'CN' => 'China',
        ];

        return view('checkout.shipping', [
            'shipping' => $shipping,
            'countries' => $countries,
        ]);
    }

    public function storeShipping(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'phone' => 'nullable|string|max:50',
        ]);

        session(['shipping' => $data]);


        return redirect()->route('checkout.review');
    }

    public function review()
    {
        $cart = session('cart', []);
        $shipping = session('shipping', []);

        return view('checkout.review', [
            'cart' => $cart,
            'shipping' => $shipping,
        ]);
    }
    public function cartIndex()
    {
        $cart = session('cart', []);

        // Normalize cart: if it's stored as a JSON/string, try to decode; ensure it's an array
        if (is_string($cart)) {
            $decoded = json_decode($cart, true);
            $cart = is_array($decoded) ? $decoded : [];
        }
        if (!is_array($cart)) {
            $cart = [];
        }

        $total = 0;
        foreach ($cart as $id => $item) {
            if (!is_array($item)) {
                // skip invalid entries
                continue;
            }
            $price = isset($item['price']) ? (float)$item['price'] : 0;
            $quantity = isset($item['quantity']) ? (int)$item['quantity'] : 0;
            $subtotal = $price * $quantity;
            $cart[$id]['subtotal'] = $subtotal;
            $total += $subtotal;
        }

        return view('cart.index', [
            'cart' => $cart,
            'total' => $total,
        ]);
    }

    public function cartStore(Request $request)
    {
        $data = $request->validate([
            'productId' => 'required|integer',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($data['productId']);

        $cart = session('cart', []);

        // Normalize existing cart (same logic)
        if (is_string($cart)) {
            $decoded = json_decode($cart, true);
            $cart = is_array($decoded) ? $decoded : [];
        }
        if (!is_array($cart)) {
            $cart = [];
        }

        $id = (string)$product->id;
        $qty = (int)$data['quantity'];

        if (!empty($product->image)) {
            if (preg_match('/^(https?:)?\/\//', $product->image) || str_starts_with($product->image, '/')) {
                $imageUrl = $product->image;
            } else {
                $imageUrl = asset('storage/' . $product->image);
            }
        } elseif (!empty($product->image_path)) {
            $imageUrl = asset('storage/' . $product->image_path);
        } else {
            $imageUrl = asset('images/default-product.png');
        }

        if (isset($cart[$id]) && is_array($cart[$id])) {
            $cart[$id]['quantity'] = ($cart[$id]['quantity'] ?? 0) + $qty;
        } else {
            $cart[$id] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $qty,
                'image_url' => $imageUrl,
            ];
        }

        session(['cart' => $cart]);

        return redirect()->route('cart.index')->with('success', '商品をカートに追加しました。');
    }

    public function cartUpdate(Request $request)
    {
        $data = $request->validate([
            'productId' => 'required|integer',
            'quantity' => 'required|integer|min:0',
        ]);

        $cart = session('cart', []);

        // Normalize cart
        if (is_string($cart)) {
            $decoded = json_decode($cart, true);
            $cart = is_array($decoded) ? $decoded : [];
        }
        if (!is_array($cart)) {
            $cart = [];
        }

        $id = (string)$data['productId'];
        $qty = (int)$data['quantity'];

        if (isset($cart[$id]) && is_array($cart[$id])) {
            if ($qty <= 0) {
                unset($cart[$id]);
            } else {
                $cart[$id]['quantity'] = $qty;
            }
            session(['cart' => $cart]);
        }

        return redirect()->route('cart.index');
    }

    public function cartRemove(Request $request)
    {
        $data = $request->validate([
            'productId' => 'required|integer',
        ]);

        $cart = session('cart', []);

        // Normalize cart
        if (is_string($cart)) {
            $decoded = json_decode($cart, true);
            $cart = is_array($decoded) ? $decoded : [];
        }
        if (!is_array($cart)) {
            $cart = [];
        }

        $id = (string)$data['productId'];

        if (isset($cart[$id]) && is_array($cart[$id])) {
            unset($cart[$id]);
            session(['cart' => $cart]);
        }

        return redirect()->route('cart.index');
    }

    public function cartClear()
    {
        session()->forget('cart');
        return redirect()->route('cart.index');
    }
}
