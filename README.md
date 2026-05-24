# LaravelRetail

A billing, inventory, and accounting web app built with **Laravel 11** for small retail businesses in India.

## Features

| Module | Features |
|--------|----------|
| **Sales** | Sale invoices, estimates/quotations, delivery challan, credit notes |
| **Purchase** | Purchase bills, debit notes |
| **Parties** | Customers, suppliers, GSTIN, opening balance, ledger |
| **Inventory** | Items with HSN, GST %, SKU/barcode, stock tracking, low-stock alerts |
| **Payments** | Payment in/out, link to invoices, cash & bank accounts |
| **Expenses** | Categorized expenses with account deduction |
| **GST** | CGST/SGST/IGST, inter-state toggle, GST summary report |
| **Reports** | Sales, purchase, stock valuation, party ledger, P&L, low stock |
| **Print** | Invoice print view + PDF download |
| **Dashboard** | Today/month sales, receivable/payable, low stock, recent bills |

## Requirements

- XAMPP (PHP 8.2+, MySQL, Apache)
- [Composer](https://getcomposer.org)

### Default login

- **Email:** `admin@laravelretail.com`
- **Password:** `password`

```
DB_DATABASE=laravel_retail
DB_USERNAME=root
DB_PASSWORD=
```

## Apache note

Point your browser to the **`public`** folder. For cleaner URLs, add a virtual host or alias to `public/`.

## Tech stack

- Laravel 11
- MySQL
- Bootstrap 5 + Bootstrap Icons
- DomPDF for invoice PDFs
