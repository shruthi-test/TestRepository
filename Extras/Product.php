<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Product;
use DB;
use App\AttributeCombination;


class Product extends Model
{
    //
    protected $table = "products";



    public function Categories()
    {

        return $this->belongsToMany('\App\Category'::class, 'product_categories')->withTimestamps();
    }

    public function init_get_categories()
    {

        return $this->belongsToMany('\App\Category'::class, 'product_categories')->withTimestamps();
    }


     public function init_attributes() {

            return $this->hasMany(AttributeCombination::class,'product_id','id');
        }

    public function productattributes() {

        return $this->init_attributes()->where('active_status','=', 1);

     }


    //  public function default_variant() {

    //     return $this->hasOne(AttributeCombination::class,'default_select',1);
    // }


     public function allproducts_init_attributes() {

        return $this->hasOne(AttributeCombination::class,'product_id','id')->where('default_select',1);
    }

     public function allproductsproductattributes() {

        return $this->allproducts_init_attributes()->where('active_status','=', 1);

     }


     public function init_avg_rate() {

        return $this->hasMany('App\Review');
     }

      public function avg_rate() {

         return $this->init_avg_rate()->where('active_status','=', 1)->avg('rating');

        // return $this->init_avg_rate()->where('active_status','=', 1)->avg('rating');
     }

     public function reviews() {

        return $this->hasMany('App\Review');
    }

    public function bestselling() {

        return $this->hasMany('App\OrderDetail');
    }

    // public function bestsellingfeature() {

    //     return $this->init_bestselling()->where('feature_qty','=','Placed');
    // }









    // public function getCategories() {

    //     return $this->init_get_categories()->where('id',$category_id)->get();
    // }

    public function UpdateCategories()
    {

        return $this->belongsToMany('\App\Category'::class, 'product_categories');
    }

    public function taxrules()
    {
      return $this->belongsTo('App\TaxRule','tax_rule_id','id');
    }

    public function attributes() {
      return $this->hasMany('App\ProductAttribute');
    }

    public static function product_detail($slug) {

        $product_detail = Product::where('slug',$slug)->where('active_status',1)->first();

        return $product_detail;
    }

    public function get_categories() {
        return $this->belongsToMany(Category::class, 'product_categories', 'product_id', 'category_id');
            // return $this->belongsToMany('App\Category','product_categories');
        }

        public function gallery() {

            return $this->hasMany(ProductGallery::class,'product_id','id');
        }

        public function init_features() {

            return $this->hasMany(ProductAttribute::class,'product_id','id');
        }

        public function features() {

            return $this->init_features()->where('active_status','=', 1);
         }

        public static function related_products($id,$prodcatarray) {


            $productsids = ProductCategory::join('products','product_categories.product_id','=','products.id')
                                            ->whereIn('product_categories.category_id',$prodcatarray)
                                            ->where('product_categories.product_id','!=',$id)
                                            ->where('products.active_status',1)
                                            ->select('product_categories.id as product_category_id','products.*')
                                            ->get();



            $finalarray = array();

            foreach($productsids as $prod) {

                if($prod->combinations == "Yes") {

                    $comb_check = AttributeCombination::where('product_id',$prod->id)->where('active_status',1)->orderBy('id','ASC')->limit(1)->first();

                    if($comb_check) {

                    $prodresponse['id'] = $prod->id;
                    $prodresponse['image'] = $prod->image;
                    $prodresponse['product_name'] = $prod->product_name;
                    $prodresponse['slug'] = $prod->slug;
                    $prodresponse['description'] = $prod->description;
                    $prodresponse['quantity'] = $prod->quantity;
                    $prodresponse['tax_inc_price'] = $prod->tax_inc_price;
                    $prodresponse['tax_excl_price'] = $prod->tax_excl_price;
                    $prodresponse['shipping_price'] = $prod->shipping_price;
                    $prodresponse['product_code'] = $prod->product_code;
                    $prodresponse['max_quantity'] = $prod->max_quantity;
                    $prodresponse['percent'] = $prod->percent;
                    $prodresponse['active_status'] = $prod->active_status;

                    $averagerate = Review::where('product_id',$prod->id)
                                     ->where('active_status',1)
                                     ->avg('rating');

                    $reviewstat = $averagerate;
                    $prodresponse['avg_rate'] = $reviewstat;
                    $prodresponse['attributes'] = AttributeCombination::where('product_id',$prod->id)->where('active_status',1)->get();

                    }

                } else {

                    $prodresponse['id'] = $prod->id;
                    $prodresponse['image'] = $prod->image;
                    $prodresponse['product_name'] = $prod->product_name;
                    $prodresponse['slug'] = $prod->slug;
                    $prodresponse['description'] = $prod->description;
                    $prodresponse['quantity'] = $prod->quantity;
                    $prodresponse['tax_inc_price'] = $prod->tax_inc_price;
                    $prodresponse['tax_excl_price'] = $prod->tax_excl_price;
                    $prodresponse['shipping_price'] = $prod->shipping_price;
                    $prodresponse['product_code'] = $prod->product_code;
                    $prodresponse['max_quantity'] = $prod->max_quantity;
                    $prodresponse['percent'] = $prod->percent;
                    $prodresponse['active_status'] = $prod->active_status;

                    $averagerate = Review::where('product_id',$prod->id)
                                     ->where('active_status',1)
                                     ->avg('rating');

                    $reviewstat = $averagerate;
                    $prodresponse['avg_rate'] = $reviewstat;
                    $prodresponse['attributes'] = AttributeCombination::where('product_id',$prod->id)->where('active_status',1)->get();

                }

                    array_push($finalarray,$prodresponse);
            }

            return $finalarray;
        }



        public static function test_related_products($id,$prodcatarray) {


            $productsids = ProductCategory::join('products','product_categories.product_id','=','products.id')
                                            ->whereIn('product_categories.category_id',$prodcatarray)
                                            ->where('product_categories.product_id','!=',$id)
                                            ->where('products.active_status',1)
                                            ->select('product_categories.id as product_category_id','products.*')
                                            ->get();



            $finalarray = array();

            foreach($productsids as $prod) {

                    $prodresponse['attributes'] = AttributeCombination::where('product_id',$prod->id)->where('active_status',1)->get();

                    if(sizeof($prodresponse['attributes']) > 0) {

                        $varientdefaultcheck = AttributeCombination::where('product_id',$proddet->id)->where('active_status',1)->where('default_select',1)->first();

                        if($varientdefaultcheck) {

                            $dflt_prod_det = Product::where('id',$varientdefaultcheck->product_id)->first();

                            $prodresponse['id'] = $dflt_prod_det->id;
                            $prodresponse['image'] = $varientdefaultcheck->image;
                            $prodresponse['image_url'] = URL('/').'/resize_product_combination_photos';
                            $prodresponse['product_name'] = $dflt_prod_det->product_name;
                            $prodresponse['product_code'] = $varientdefaultcheck->product_code;
                            $prodresponse['var_product_name'] = $varientdefaultcheck->product_name;
                            $prodresponse['slug'] = $dflt_prod_det->slug;
                            $prodresponse['description'] = $dflt_prod_det->description;
                            $prodresponse['quantity'] = $varientdefaultcheck->variant_qty;
                            $prodresponse['tax_inc_price'] = $varientdefaultcheck->variant_price_tax_inc;
                            $prodresponse['tax_excl_price'] = $varientdefaultcheck->variant_price_tax_excl;
                            $prodresponse['shipping_price'] = $dflt_prod_det->shipping_price;
                            $prodresponse['max_quantity'] = $varientdefaultcheck->variant_max_qty;
                            $prodresponse['percent'] = $dflt_prod_det->percent;
                            $prodresponse['active_status'] = $dflt_prod_det->active_status;

                            $averagerate = Review::where('product_id',$dflt_prod_det->id)
                                             ->where('active_status',1)
                                             ->avg('rating');

                            $reviewstat = $averagerate;
                            $prodresponse['avg_rate'] = $reviewstat;

                            $prodresponse['attributes'] = AttributeCombination::where('product_id',$dflt_prod_det->id)->where('active_status',1)->get();

                        } else {

                            $varient_default_check = AttributeCombination::where('product_id',$prod->id)->where('active_status',1)->orderBy('id','ASC')->limit(1)->first();

                            if($varient_default_check) {

                                $dflt_prod_det = Product::where('id',$varient_default_check->product_id)->first();

                                $prodresponse['id'] = $dflt_prod_det->id;
                                $prodresponse['image'] = $varient_default_check->image;
                                $prodresponse['image_url'] = URL('/').'/resize_product_combination_photos';
                                $prodresponse['product_name'] = $dflt_prod_det->product_name;
                                $prodresponse['product_code'] = $varient_default_check->product_code;
                                $prodresponse['var_product_name'] = $varient_default_check->product_name;
                                $prodresponse['slug'] = $dflt_prod_det->slug;
                                $prodresponse['description'] = $dflt_prod_det->description;
                                $prodresponse['quantity'] = $varient_default_check->variant_qty;
                                $prodresponse['tax_inc_price'] = $varient_default_check->variant_price_tax_inc;
                                $prodresponse['tax_excl_price'] = $varient_default_check->variant_price_tax_excl;
                                $prodresponse['shipping_price'] = $dflt_prod_det->shipping_price;
                                $prodresponse['max_quantity'] = $varient_default_check->variant_max_qty;
                                $prodresponse['percent'] = $dflt_prod_det->percent;
                                $prodresponse['active_status'] = $dflt_prod_det->active_status;

                                $averagerate = Review::where('product_id',$dflt_prod_det->id)
                                                 ->where('active_status',1)
                                                 ->avg('rating');

                                $reviewstat = $averagerate;
                                $prodresponse['avg_rate'] = $reviewstat;

                                $prodresponse['attributes'] = AttributeCombination::where('product_id',$dflt_prod_det->id)->where('active_status',1)->get();

                            }

                        }

                    } else {

                        if($prod->combinations == 'No') {

                            $prodresponse['id'] = $prod->id;
                            $prodresponse['image'] = $prod->image;
                            $prodresponse['image_url'] = URL('/').'/resize_product_photos';
                            $prodresponse['product_name'] = $prod->product_name;
                            $prodresponse['product_code'] = $prod->product_code;
                            $prodresponse['var_product_name'] = '';
                            $prodresponse['slug'] = $prod->slug;
                            $prodresponse['description'] = $prod->description;
                            $prodresponse['quantity'] = $prod->quantity;
                            $prodresponse['tax_inc_price'] = $prod->tax_inc_price;
                            $prodresponse['tax_excl_price'] = $prod->tax_excl_price;
                            $prodresponse['shipping_price'] = $prod->shipping_price;
                            $prodresponse['max_quantity'] = $prod->max_quantity;
                            $prodresponse['percent'] = $prod->percent;
                            $prodresponse['active_status'] = $prod->active_status;

                            $averagerate = Review::where('product_id',$prod->id)
                                             ->where('active_status',1)
                                             ->avg('rating');

                            $reviewstat = $averagerate;
                            $prodresponse['avg_rate'] = $reviewstat;

                            $prodresponse['attributes'] = AttributeCombination::where('product_id',$prod->id)->where('active_status',1)->get();

                        }

                    }
                    
                    array_push($finalarray,$prodresponse);
            }

            return $finalarray;
        }



        public static function most_viewed_products() {


            $productsids = ProductCategory::join('products','product_categories.product_id','=','products.id')
                                            ->join('most_viewed_products','most_viewed_products.product_id','=','products.id')
                                            ->where('products.active_status',1)
                                            ->orderBy('most_viewed_products.count','DESC')
                                            ->select('product_categories.id as product_category_id','products.*')
                                            ->get();


            $finalarray = array();

            foreach($productsids as $prod) {

                    $proddet = Product::where('id',$prod->id)->where('active_status',1)->first();

                    $prodresponse['attributes'] = AttributeCombination::where('product_id',$proddet->id)->where('active_status',1)->get();

                    if(sizeof($prodresponse['attributes']) > 0) {

                        $varientdefaultcheck = AttributeCombination::where('product_id',$proddet->id)->where('active_status',1)->where('default_select',1)->first();

                        if($varientdefaultcheck) {

                            $dflt_prod_det = Product::where('id',$varientdefaultcheck->product_id)->first();

                            $prodresponse['id'] = $dflt_prod_det->id;
                            $prodresponse['image'] = $varientdefaultcheck->image;
                            $prodresponse['image_url'] = URL('/').'/resize_product_combination_photos';
                            $prodresponse['product_name'] = $dflt_prod_det->product_name;
                            $prodresponse['product_code'] = $varientdefaultcheck->product_code;
                            $prodresponse['var_product_name'] = $varientdefaultcheck->product_name;
                            $prodresponse['slug'] = $dflt_prod_det->slug;
                            $prodresponse['description'] = $dflt_prod_det->description;
                            $prodresponse['quantity'] = $varientdefaultcheck->variant_qty;
                            $prodresponse['tax_inc_price'] = $varientdefaultcheck->variant_price_tax_inc;
                            $prodresponse['tax_excl_price'] = $varientdefaultcheck->variant_price_tax_excl;
                            $prodresponse['shipping_price'] = $dflt_prod_det->shipping_price;
                            $prodresponse['max_quantity'] = $varientdefaultcheck->variant_max_qty;
                            $prodresponse['percent'] = $dflt_prod_det->percent;
                            $prodresponse['active_status'] = $dflt_prod_det->active_status;

                            $averagerate = Review::where('product_id',$dflt_prod_det->id)
                                             ->where('active_status',1)
                                             ->avg('rating');

                            $reviewstat = $averagerate;
                            $prodresponse['avg_rate'] = $reviewstat;

                            $prodresponse['attributes'] = AttributeCombination::where('product_id',$dflt_prod_det->id)->where('active_status',1)->get();

                        } else {

                            $varient_default_check = AttributeCombination::where('product_id',$proddet->id)->where('active_status',1)->orderBy('id','ASC')->limit(1)->first();

                            if($varient_default_check) {

                                $dflt_prod_det = Product::where('id',$varient_default_check->product_id)->first();

                                $prodresponse['id'] = $dflt_prod_det->id;
                                $prodresponse['image'] = $varient_default_check->image;
                                $prodresponse['image_url'] = URL('/').'/resize_product_combination_photos';
                                $prodresponse['product_name'] = $dflt_prod_det->product_name;
                                $prodresponse['product_code'] = $varient_default_check->product_code;
                                $prodresponse['var_product_name'] = $varient_default_check->product_name;
                                $prodresponse['slug'] = $dflt_prod_det->slug;
                                $prodresponse['description'] = $dflt_prod_det->description;
                                $prodresponse['quantity'] = $varient_default_check->variant_qty;
                                $prodresponse['tax_inc_price'] = $varient_default_check->variant_price_tax_inc;
                                $prodresponse['tax_excl_price'] = $varient_default_check->variant_price_tax_excl;
                                $prodresponse['shipping_price'] = $dflt_prod_det->shipping_price;
                                $prodresponse['max_quantity'] = $varient_default_check->variant_max_qty;
                                $prodresponse['percent'] = $dflt_prod_det->percent;
                                $prodresponse['active_status'] = $dflt_prod_det->active_status;

                                $averagerate = Review::where('product_id',$dflt_prod_det->id)
                                                 ->where('active_status',1)
                                                 ->avg('rating');

                                $reviewstat = $averagerate;
                                $prodresponse['avg_rate'] = $reviewstat;

                                $prodresponse['attributes'] = AttributeCombination::where('product_id',$dflt_prod_det->id)->where('active_status',1)->get();

                            }

                        }


                    } else {

                        if($proddet->combinations == 'No') {

                            $prodresponse['id'] = $proddet->id;
                            $prodresponse['image'] = $proddet->image;
                            $prodresponse['image_url'] = URL('/').'/resize_product_photos';
                            $prodresponse['product_name'] = $proddet->product_name;
                            $prodresponse['product_code'] = $proddet->product_code;
                            $prodresponse['var_product_name'] = '';
                            $prodresponse['slug'] = $proddet->slug;
                            $prodresponse['description'] = $proddet->description;
                            $prodresponse['quantity'] = $proddet->quantity;
                            $prodresponse['tax_inc_price'] = $proddet->tax_inc_price;
                            $prodresponse['tax_excl_price'] = $proddet->tax_excl_price;
                            $prodresponse['shipping_price'] = $proddet->shipping_price;
                            $prodresponse['max_quantity'] = $proddet->max_quantity;
                            $prodresponse['percent'] = $proddet->percent;
                            $prodresponse['active_status'] = $proddet->active_status;

                            $averagerate = Review::where('product_id',$proddet->id)
                                             ->where('active_status',1)
                                             ->avg('rating');

                            $reviewstat = $averagerate;
                            $prodresponse['avg_rate'] = $reviewstat;

                            $prodresponse['attributes'] = AttributeCombination::where('product_id',$proddet->id)->where('active_status',1)->get();

                        }

                    }
                    
                    array_push($finalarray,$prodresponse);
            }

            // $len = count($finalarray);
            // for($i=0;$i<$len;$i++) {
            //     $finalarray[$i] = (Object)$finalarray[$i];
            // }

            return $finalarray;
        }



        public function attributes_get() {

         return $this->belongsToMany(ProductAttribute::class,Product::class);

        }



        public function init_product_variants() {

            return $this->hasMany('App\AttributeCombination');
        }



        public function productvariants() {

            return $this->init_product_variants()->where('active_status','=', 1);
        }

        public function userproducts() {

            return $this->belongsToMany('App\User');
        }

        public static function getCalculateOffer($basePrice,$percent) {

            $offerPrice = ($basePrice - ($basePrice * ($percent/100)));

            return $offerPrice;
        }


        public static function getShippingCost($product_id) {

            $productDet = Product::where('id',$product_id)->where('active_status',1)->first();

            if($productDet) {

                $shippingprice = $productDet->shipping_price;

            } else {

                $shippingprice = 0;
            }

            return $shippingprice;
        }

        public static function productNameById($id) {

            $productdet = Product::where('id',$id)->where('active_status',1)->first();
            $productName = '';

            if($productdet) {

                $productName = $productdet->product_name;

            } else {

                $productName = '';

            }

            return $productName;
        }
        // public static function product_detail($slug) {

        // $product_detail = Product::where('slug',$slug)->where('active_status',1)->first();

        // return $product_detail;
        // }


        // public static function pc_permutation($items, $perms = array( )) {

        //     if (empty($items)) {
        //         print join(' ', $perms) . "\n";
        //     }  else {
        //         for ($i = count($items) - 1; $i >= 0; --$i) {
        //              $newitems = $items;
        //              $newperms = $perms;
        //              list($foo) = array_splice($newitems, $i, 1);
        //              array_unshift($newperms, $foo);
        //              pc_permute($newitems, $newperms);
        //              }
        //         }
        // }






}
