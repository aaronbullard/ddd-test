<?php

namespace Tests\Feature;

use Tests\TestCase;

abstract class InvoiceTest extends TestCase
{
    public function fake_anemic_invoice_test()
    {
        $invoice = new Invoice();
        $invoice->setCustomer($this->customer);
        $invoice->setDate(new DateTime);

        // Line Items
        $li = new InvoiceLineItem();
        $li->setProduct($this->product);
        $li->setQuantity(1);
        $li->setInvoice($invoice);

        $lineItems = $invoice->getLineItems();
        $lineItems[] = $li;

        $invoice->setLineItems($lineItems);

        // Total
        $total = [];
        foreach($invoice->getLineItems() as $li){
            $total += $li->getProduct()->getCost() * $li->getQuantity();
        }

        $invoice->setTotal($total);

        $this->assertEqual($total, $invoice->getTotal());
    }

    public function fake_strong_invoice_test($value='')
    {
        $invoice = new Invoice($this->customer);

        $invoice->addProduct($this->product, 1);

        $invoice->getTotal();
    }
}

class Invoice
{
    protected $customer;

    protected $date;

    protected $lineItems;

    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
        $this->date = new DateTime();
    }

    public function setDate(DateTime $date)
    {
        $this->date = $date;
    }

    public function addProduct(Product $product, $quantity = 1)
    {
        $this->lineItems[] = new LineItem($product, $quantity);
    }

    public function getTotal()
    {
        return array_reduce($this->lineItems, function($total, $li){
            return $total += $li->getTotal();
        }, 0);
    }
}
