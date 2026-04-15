<?php

function getProductStatus(int $quantity): string
{
    if ($quantity <= 0) {
        return 'Out of stock';
    }

    if ($quantity <= 5) {
        return 'Low stock';
    }

    return 'Available';
}

function getStatusClass(string $status): string
{
    if ($status === 'Available') {
        return 'status-available';
    }

    if ($status === 'Low stock') {
        return 'status-low';
    }

    return 'status-out';
}

function getTotalQuantity(array $products): int
{
    return array_reduce($products, function ($carry, $product) {
        return $carry + $product['quantity'];
    }, 0);
}

function getAvailableProducts(array $products): array
{
    return array_values(array_filter($products, function ($product) {
        return $product['quantity'] > 0;
    }));
}

function getCategories(array $products): array
{
    $categories = array_map(function ($product) {
        return $product['category'];
    }, $products);

    $categories = array_unique($categories);
    sort($categories);

    return $categories;
}

function searchProducts(array $products, string $keyword): array
{
    $keyword = trim($keyword);

    if ($keyword === '') {
        return $products;
    }

    return array_values(array_filter($products, function ($product) use ($keyword) {
        return stripos($product['name'], $keyword) !== false
            || stripos($product['category'], $keyword) !== false
            || stripos($product['brand'], $keyword) !== false;
    }));
}

function filterProducts(array $products, string $category, string $status): array
{
    return array_values(array_filter($products, function ($product) use ($category, $status) {
        $matchCategory = $category === 'all' || $product['category'] === $category;
        $matchStatus = $status === 'all' || getProductStatus($product['quantity']) === $status;

        return $matchCategory && $matchStatus;
    }));
}