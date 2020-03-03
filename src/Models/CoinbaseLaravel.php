<?php
    // MyVendor\Contactform\src\Models\ContactForm.php
    namespace MyVendor\Contactform\Models;
    use Illuminate\Database\Eloquent\Model;
    class ContactForm extends Model
    {
        protected $guarded = [];
        protected $table = 'coinbase_transactions';
    }