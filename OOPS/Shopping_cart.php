<?php

class Product
{
    private $id;
    private $name;
    private $price;
    private $stock;

    public function __construct($id, $name, $price, $stock)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->stock = $stock;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getStock()
    {
        return $this->stock;
    }

    public function displayProduct()
    {
        echo "ID: " . $this->id . "\n";
        echo "Name: " . $this->name . "\n";
        echo "Price: ₹" . $this->price . "\n";
        echo "Stock: " . $this->stock . "\n";
    }
}


class Cart
{
    private $items = [];

    public function addItem(Product $product, $quantity)
    {
        if ($quantity <= $product->getStock()) {

            $this->items[] = [
                "product" => $product,
                "quantity" => $quantity
            ];

            echo $product->getName() . " added to cart.\n";
        } 
        else {
            echo "Not enough stock.\n";
        }
    }

    public function removeItem($productId)
    {
        foreach ($this->items as $key => $item) {

            if ($item["product"]->getId() == $productId) {

                unset($this->items[$key]);

                echo "Product removed from cart.\n";
                return;
            }
        }

        echo "Product not found.\n";
    }

    public function updateQuantity($productId, $quantity)
    {
        foreach ($this->items as &$item) {

            if ($item["product"]->getId() == $productId) {

                $item["quantity"] = $quantity;

                echo "Quantity updated.\n";
                return;
            }
        }

        echo "Product not found.\n";
    }

    public function calculateTotal()
    {
        $total = 0;

        foreach ($this->items as $item) {

            $total += $item["product"]->getPrice()
                    * $item["quantity"];
        }

        return $total;
    }

    public function displayCart()
    {
        echo "<h3>Shopping Cart</h3>";

        if (empty($this->items)) {
            echo "Cart is empty.\n";
            return;
        }

        foreach ($this->items as $item) {

            $product = $item["product"];
            $quantity = $item["quantity"];

            echo "Product: " . $product->getName() . "\n";
            echo "Price: ₹" . $product->getPrice() . "\n";
            echo "Quantity: " . $quantity . "\n";
            echo "Subtotal: ₹" .
                 ($product->getPrice() * $quantity) . "\n";
        }

        echo "<b>Total: ₹" . $this->calculateTotal() . "</b>\n";
    }
}


class Customer
{
    private $id;
    private $name;
    private $email;

    public function __construct($id, $name, $email)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
    }

    public function displayCustomer()
    {
        echo "<h3>Customer Details</h3>";

        echo "Customer ID: " . $this->id . "\n";
        echo "Name: " . $this->name . "\n";
        echo "Email: " . $this->email . "\n";
    }
}


// ------------------------------------
// Objects
// ------------------------------------

$product1 = new Product(101, "Laptop", 50000, 5);
$product2 = new Product(102, "Mouse", 800, 10);
$product3 = new Product(103, "Keyboard", 1500, 8);

$cart = new Cart();

$customer = new Customer(
    1,
    "Nayana",
    "nayana@gmail.com"
);


// ------------------------------------
// Switch Menu
// ------------------------------------

$choice = 1;

switch ($choice) {

    case 1:

        echo "<h2>Available Products</h2>";

        $product1->displayProduct();
        $product2->displayProduct();
        $product3->displayProduct();

        break;


    case 2:

        $cart->addItem($product1, 1);

        break;


    case 3:

        $cart->addItem($product2, 2);

        break;


    case 4:

        $cart->displayCart();

        break;


    case 5:

        $cart->updateQuantity(102, 3);

        break;


    case 6:

        $cart->removeItem(102);

        break;


    case 7:

        $customer->displayCustomer();

        break;


    case 8:

        echo "<h3>Checkout</h3>";

        echo "Customer: Nayana\n";
        echo "Total Amount: ₹" .
             $cart->calculateTotal() . "\n";

        echo "Order placed successfully!";

        break;


    default:

        echo "Invalid choice.";
}
?>