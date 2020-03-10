<?php
    namespace coinbaselaravel\Models;
    use Illuminate\Database\Eloquent\Model;
    class CoinbaseLaravel extends Model
    {
        protected $guarded = [];
        protected $table = 'coinbase_transactions';

        public function user()
        {
            return $this->belongsTo('App\User');
        }
    }