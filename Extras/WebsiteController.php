<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Setting;
use View;
use App\Category;
use App\Slider;
use App\Product;
use DB;
use App\ProductAttribute;
use Session;
use App\Cart;
use Auth;
use App\Http\Traits\CartTrait;
use App\OrderDetail;
use App\Mostviewedproducts;
use App\Blog;
use App\Testimonial;
use App\Http\Traits\ReviewTrait;
use App\Http\Traits\ProductTrait;
use App\Http\Traits\AttributeTrait;
use App\ProductCombinationGallery;
use App\ProductGallery;
use App\ProductCategory;
use App\TaxRule;
use App\Cms;
use App\Brand;
use App\AttributeCombination;
use App\ProductDetailValue;
use App\Attribute;
use App\AttributeValue;
use App\Offer;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Traits\CouponTrait;
use App\WishList;
use App\User;
use Mail;

class WebsiteController extends Controller
{
	use CartTrait;
	use ReviewTrait;
	use ProductTrait;
	use AttributeTrait;
	use CouponTrait;

	public function __construct(Request $request)
    {
      $get_settings = Setting::getWebsiteSettings();
      $logo_get = Setting::where('name','=','logo')->first();
      $data['title'] = $get_settings['title'];
      $data['logo'] = $get_settings['logo'];

      $settingslist = Setting::all();
      $meta_description = '';
      $meta_keywords = '';

      foreach($settingslist as $column_name => $list) {

      	if($column_name == "meta_description") {

      		$meta_description = $list;
      	}

      	if($column_name == "meta_keywords") {

      		$meta_keywords = $list;
      	}
      }

      $data['meta_description'] = $meta_description;
      $data['meta_keywords']   = $meta_keywords;

      $data['main_categories'] = Category::where('parent_id',0)->where('active_status',1)->get();

      $data['testimonials'] = Testimonial::where('active_status',1)->get();
      $data['offer_products'] = Product::with('allproductsproductattributes')->with('productvariants')->where('active_status',1)->where('percent','>',0)->orderBy(DB::raw('RAND()'))
        ->take(3)->get();
      $data['brands'] = Brand::where('active_status',1)->get();

       $this->middleware(function ($request, $next) {
       	View::share('cart_count', Cart::get_cart_count());
       	View::share('cart_price', Cart::get_cart_price());
            // $this->carts = Cart::get_cart_count();

            return $next($request);
        });


      View::share('data', $data);
    }

    public function errorpage() {
		return view('userfiles.404');
	}

	public function getHomePage() {

		$sliders = Slider::all();

		$blogs = Blog::where('active_status',1)->take(10)->get();

		$latest_products = Product::with('allproductsproductattributes')->with('productvariants')
							->where('active_status',1)
							->orderBy('created_at','DESC')
							->take(6)
							->get();

		$bestsellers = Product::with('allproductsproductattributes')->has('bestselling', '>' , 0)->with('productvariants')->withCount(['bestselling as bestsellingproduct' => function($query) {

				$query->select(DB::raw('sum(quantity)'));

			}])->withCount(['reviews as reviews_avg' => function($query) {

				$query->select(DB::raw('avg(rating)'));

			}])->withCount(['reviews as reviews_count' => function($query) {

				$query->select(DB::raw('count(rating)'));

			}])->where('active_status',1)->orderBy('bestsellingproduct','DESC')
				 ->take(8)
				 ->get();

		$featuredProducts = Product::with('productattributes')->withCount(['bestselling as bestsellingproduct' => function($query) {
					
				$query->select(DB::raw('sum(quantity)'));
			
			}])->withCount(['reviews as reviews_avg' => function($query) {
			
				$query->select(DB::raw('avg(rating)'));
			
			}])->withCount(['reviews as reviews_count' => function($query) {
			
				$query->select(DB::raw('count(rating)'));
			
			}])->where('active_status',1)->where('feature_status',1)->orderBy('created_at','DESC')
				->get();

		$most_viewed_products = Product::most_viewed_products();


		return view('userfiles.home',compact('sliders','blogs','latest_products','bestsellers','featuredProducts','most_viewed_products'));
	}


	public function getAboutusPage() {

		return view('userfiles.about');

	}

	public function getermsConditionsPage() {

		return view('userfiles.terms');

	}


	public function getPrivacyPolicyPage() {

		return view('userfiles.privacy_policy');

	}


	public function getProductPage(Request $request, $categoryslug) {

		if(isset($_GET['attr'])) {

			$attrval = $_GET['attr'];
			$attrarray = explode('|', $attrval);
		} else {

			$attrval = null;
			$attrarray = array();
		}

		$productsids  = array();


		if(sizeof($attrarray) > 0) {

		$getids = $this->getProductBySlug($attrarray);

		foreach($getids as $getid) {

			if(!in_array($getid->id, $productsids)) {

				array_push($productsids, $getid->id);

				}

			}

		}



		$category_detail = Category::category_detail($categoryslug);

		// return $category_detail->id;
		if($category_detail) {

			$categories = Category::with('childrenRecursive')->where('id',$category_detail->id)->first();
			$childrens = Category::where('parent_id',$category_detail->id)->get();

			if(sizeof($childrens) > 0) {
				$relate_categories = Category::where('parent_id',$category_detail->id)
											->where('id','!=',$category_detail->id)->where('active_status',1)->get();

				$category_parent = Category::with('parent')->where('id',$category_detail->id)->first();

				$get_depth = $this->getDepth($category_parent);

				$category_level = $get_depth['level'];
				$parent_category_ids = $get_depth['relate_array'];
				$parent_category_ids_ignore = $get_depth['id_array'];




			$related_categories = Category::whereIn('categories.parent_id', $parent_category_ids)
                                              ->where('categories.id','!=',$category_detail->id)
                                              ->whereNOTIn('categories.id',$parent_category_ids_ignore)
																							->where('active_status',1)
                                              ->where('parent_id','!=',0)->get();




				return view('userfiles.category_detail',compact('childrens','related_categories','category_detail'));

			} else {

				$category_parent = Category::with('parent')->where('id',$category_detail->id)->first();

				$get_depth = $this->getDepth($category_parent);

				$category_level = $get_depth['level'];
				$parent_category_ids = $get_depth['relate_array'];
				$parent_category_ids_ignore = $get_depth['id_array'];


				$related_categories = Category::whereIn('categories.parent_id', $parent_category_ids)
                                              ->where('categories.id','!=',$category_detail->id)
                                              ->where('categories.id','!=',$category_detail->parent_id)
                                              ->whereNOTIn('categories.id',$parent_category_ids_ignore)
																							->where('active_status',1)
                                              ->where('parent_id','!=',0)->get();


				$product_detailarr = array();


				if(isset($_GET['filter'])) {

				$price_filter = $_GET['filter'];
				} else {

					$price_filter = "none";
				}

				if(isset($_GET['pricefilter'])) {

				$pricefilter = $_GET['pricefilter'];

				$pricefilterarr = explode("-",$pricefilter);

				} else {

				$pricefilter = "none";
				}



				$categoryidget = $category_detail->id;


				$products = Product::whereHas('init_get_categories', function($q) use($categoryidget) {

					$q->where('categories.id',$categoryidget);

				})->with('productattributes')->withCount(['reviews as reviews_avg' => function($query) {

					$query->select(DB::raw('avg(rating)'));

				}])->withCount(['reviews as reviews_count' => function($query) {

					$query->select(DB::raw('count(rating)'));

				}])->withCount(['bestselling as bestsellingfeaure' => function($query) {

					$query->select(DB::raw('sum(feature_qty)'));

				}])->withCount(['bestselling as bestsellingproduct' => function($query) {

					$query->select(DB::raw('sum(quantity)'));

				}]);

				if(sizeof($attrarray) > 0) {

				// if(sizeof($productsids) > 0) {

					$products = $products->whereIn('products.id',$productsids);
				// }

			}

				if($price_filter !="none") {


					if($price_filter == "price_low") {

					$products = $products->orderBy('tax_inc_price','ASC');

					} else if($price_filter == "price_high") {

					$products = $products->orderBy('tax_inc_price','DESC');

					} else if($price_filter == "latest") {

					$products = $products->orderBy('id','DESC');

					} else if($price_filter == "bestseller") {

					$products = $products->orderBy('bestsellingproduct','DESC');

					} else if($price_filter == "reviews") {

					$products = $products->orderBy('reviews_avg','DESC');

					} else {

					$products = $products->orderBy('id','DESC');

					}

				}

				if($pricefilter!="none") {
					$min_price = $pricefilterarr[0];
					$max_price = $pricefilterarr[1];
					$products = $products->whereBetween('price_from', [$min_price, $max_price]);

				} else {
					$min_price = -1;
					$max_price = -1;
				}

				$products = $products->where('products.active_status',1)->paginate(30);


        		// if ($request->ajax()) {
		        //     return view('userfiles.productresultgrid', compact('paginatedItems','products','product_detailarr'));
						//
		        // }


		    $attributes = Attribute::with('attributevalues')->where('attributes.active_status',1)->get();



				// return $product_detailarr;
				return view('userfiles.products',compact('products','related_categories','product_detailarr','category_detail','categoryslug','paginatedItems','attributes','attrarray','max_price','min_price'));
			}

		} else {
			$products = "none";
			return redirect('/');
		}



	}


	public function postContactUs(Request $request) {

        $this->validate($request, [
            'username' => 'required|max:255',
            'email' => 'required|email',
            'mobile' =>'required|regex:/^[6-9][0-9]{9}$/|digits:10',
            'message' => 'required',
        ]);


        $emailcontent = array (
            'username' => $request->username,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'company_name' => '',
            'messagedata' => $request->message,
        );

        $from_email = $request->email;

        $insertContactUs = DB::table('contacts')->insert(['username'=>$request->username,'email'=>$request->email,'mobile_number'=>$request->mobile,'message'=>$request->message]);

        Mail::send('emails.contact', $emailcontent, function($message) use($from_email)
        {
        $message->from($from_email)->to('jayakumar@masthmysore.com')
        ->subject('Contact Message using Our Contact Form');
        });

        return redirect('/contact-us')->with('message','You have successfully sent contact Request');
       
    }


	public function getAllProductsOld() {

		if(isset($_GET['attr'])) {
			$attrval = $_GET['attr'];
			$attrarray = explode('|', $attrval);
		} else {
			$attrval = null;
			$attrarray = array();
		}
		
		$productsids  = array();
		$categoryslug = '';
			
		if(sizeof($attrarray) > 0) {
			$getids = $this->getProductBySlug($attrarray);
		
			foreach($getids as $getid) {
				if(!in_array($getid->id, $productsids)) {
					array_push($productsids, $getid->id);
				}
			}
		}
			
		$product_detailarr = array();
			
		if(isset($_GET['filter'])) {
			$price_filter = $_GET['filter'];
		} else {
			$price_filter = "none";
		}
			
		if(isset($_GET['pricefilter'])) {
			$pricefilter = $_GET['pricefilter'];
			$pricefilterarr = explode("-",$pricefilter);
		} else {
			$pricefilter = "none";
		}
			
		$products = Product::with('productattributes')->withCount(['reviews as reviews_avg' => function($query) {
						$query->select(DB::raw('avg(rating)'));
					}])->withCount(['reviews as reviews_count' => function($query) {
						$query->select(DB::raw('count(rating)'));
					}])->withCount(['bestselling as bestsellingfeaure' => function($query) {
						$query->select(DB::raw('sum(feature_qty)'));
					}])->withCount(['bestselling as bestsellingproduct' => function($query) {
						$query->select(DB::raw('sum(quantity)'));
					}]);
			
		if(sizeof($attrarray) > 0) {
			$products = $products->whereIn('products.id',$productsids);
		}
			
		if($price_filter !="none") {

			if($price_filter == "price_low") {
				$products = $products->orderBy('tax_inc_price','ASC');
			} else if($price_filter == "price_high") {
				$products = $products->orderBy('tax_inc_price','DESC');
			} else if($price_filter == "latest") {
				$products = $products->orderBy('id','DESC');
			} else if($price_filter == "bestseller") {
				$products = $products->orderBy('bestsellingproduct','DESC');
			} else if($price_filter == "reviews") {
				$products = $products->orderBy('reviews_avg','DESC');
			} else {
				$products = $products->orderBy('id','DESC');
			}

		}
			
		if($pricefilter!="none") {

			$min_price = $pricefilterarr[0];
			$max_price = $pricefilterarr[1];
			$products = $products->whereBetween('price_from', [$min_price, $max_price]);

		} else {
			$min_price = -1;
			$max_price = -1;
		}
			
		$products = $products->where('products.active_status',1)->paginate(30);

		$attributes = Attribute::with('attributevalues')->where('attributes.active_status',1)->get();

		return view('userfiles.allproducts',compact('products','attributes','attrarray','max_price','min_price','categoryslug'));

	}

	public function getAllProducts() {

		if(isset($_GET['attr'])) {
			$attrval = $_GET['attr'];
			$attrarray = explode('|', $attrval);
		} else {
			$attrval = null;
			$attrarray = array();
		}
		
		$productsids  = array();
		$categoryslug = '';
			
		if(sizeof($attrarray) > 0) {
			$getids = $this->getProductBySlug($attrarray);
		
			foreach($getids as $getid) {
				if(!in_array($getid->id, $productsids)) {
					array_push($productsids, $getid->id);
				}
			}
		}
			
		$product_detailarr = array();
			
		if(isset($_GET['filter'])) {
			$price_filter = $_GET['filter'];
		} else {
			$price_filter = "none";
		}
			
		if(isset($_GET['pricefilter'])) {
			$pricefilter = $_GET['pricefilter'];
			$pricefilterarr = explode("-",$pricefilter);
		} else {
			$pricefilter = "none";
		}
			
		$products = Product::with('allproductsproductattributes')->with('productattributes')->where('active_status',1)->get();
			
		// if(sizeof($attrarray) > 0) {
		// 	$products = $products->whereIn('products.id',$productsids);
		// }
			
		if($price_filter !="none") {

			if($price_filter == "price_low") {
				$products = $products->orderBy('tax_inc_price','ASC');
			} else if($price_filter == "price_high") {
				$products = $products->orderBy('tax_inc_price','DESC');
			} else if($price_filter == "latest") {
				$products = $products->orderBy('id','DESC');
			} else if($price_filter == "bestseller") {
				$products = $products->orderBy('bestsellingproduct','DESC');
			} else if($price_filter == "reviews") {
				$products = $products->orderBy('reviews_avg','DESC');
			} else {
				$products = $products->orderBy('id','DESC');
			}

		}
			
		if($pricefilter!="none") {

			$min_price = $pricefilterarr[0];
			$max_price = $pricefilterarr[1];
			$products = $products->whereBetween('price_from', [$min_price, $max_price]);

		} else {
			$min_price = -1;
			$max_price = -1;
		}
			
		// $products = $products->where('products.active_status',1)->paginate(30);

		$click_type = "All Products";

		$attributes = Attribute::with('attributevalues')->where('attributes.active_status',1)->get();

		return view('userfiles.products',compact('products','attributes','attrarray','max_price','min_price','categoryslug','click_type'));

	}

	public function postSearchData(Request $request) {

		if($request->query)
		{
		 $querydata = $request->get('query');
		 

		//  $data = DB::table('products')
		//    ->where('product_name', 'LIKE', "%{$query}%")
		//    ->where('active_status',1)
		//    ->get();

		$data = Product::where('active_status',1)
						->where('product_name','LIKE','%'.$querydata.'%')
						->orWhere('product_code','LIKE','%'.$querydata.'%')
						->orderBy('id','DESC')
						->get();

		$vardata = Product::join('productvariants','productvariants.product_id','=','products.id')
						->where('products.active_status',1)
						->where('productvariants.active_status',1)
						->where('productvariants.product_name','LIKE','%'.$querydata.'%')
						->orWhere('productvariants.product_code','LIKE','%'.$querydata.'%')
						->orderBy('products.id','DESC')
						->select('products.slug','productvariants.product_name as proname','productvariants.product_code as procode')
						->get();

		 $output = '<ul class="dropdown-menu" style="display:block; position:relative">';
		 $currentUrl = url('/');
		 
		 foreach($data as $row)
		 {
		  $output .= '
		  <li><a href="'.$currentUrl.'/product/'.$row->slug.'">'.$row->product_name.'</a></li>
		  ';
		 }

		 foreach($vardata as $row)
		 {
		  $output .= '
		  <li><a href="'.$currentUrl.'/product/'.$row->slug.'">'.$row->procode.' '.$row->proname.'</a></li>
		  ';
		 }

		 $output .= '</ul>';

		 echo $output;
		}

	}


	public function getDepth($category, $level = 0, $relate_array = array(), $id_array = array()) {
    if ($category->parent_id>0) {
        if ($category->parent) {
        		$parent_id = $category->parent->id;
        		$get_id = $category->id;
                $level++;
                array_push($relate_array, $parent_id);
                array_push($id_array, $get_id);
                return $this->getDepth($category->parent, $level, $relate_array, $id_array);
            }
        }
        $data['level'] = $level;
        $data['relate_array'] = $relate_array;
        $data['id_array'] = $id_array;
       return $data;
    }

    public function getRelateCategories($category, $relate_array = array(), $id_array = array()) {


    	if ($category->parent_id>0) {

        if ($category->parent) {
        	$parent_id = $category->parent->id;
        	$get_id = $category->id;
        	array_push($relate_array, $parent_id);
        	array_push($id_array, $get_id);
        	// $relate_array[] = $parent_id;


                return $this->getRelateCategories($category->parent, $relate_array,$id_array);
            }
        }
       return $relate_array;
    }

    public function getRelateCategoriesIds($category, $id_array = array()) {


    	if ($category->parent_id>0) {

        if ($category->parent) {

        	$get_id = $category->id;
        	array_push($id_array, $get_id);
        	// $relate_array[] = $parent_id;


                return $this->getRelateCategoriesIds($category->parent,$id_array);
            }
        }
       return $id_array;
    }

	public function getProductDetail($slug,Request $request) {

		if($slug) {

		$review_status = '';
		$max_quantity = -1;
		$product_detail = Product::product_detail($slug);

		if(!$product_detail) {

			return redirect('/');
		}



		/*********** New ************/


		$prodresponseattributes = AttributeCombination::where('product_id',$product_detail->id)->where('active_status',1)->get();

        if(sizeof($prodresponseattributes) > 0) {

            $varient_default_check = AttributeCombination::where('product_id',$product_detail->id)->where('active_status',1)->where('default_select',1)->first();

            if($varient_default_check) {

                    $dflt_prod_det = Product::where('id',$varient_default_check->product_id)->first();

                    $productdetail['id'] = $dflt_prod_det->id;
                    $productdetail['image'] = $varient_default_check->image;
                    $productdetail['image_url'] = URL('/').'/product_combination_photos';
                    $productdetail['product_name'] = $dflt_prod_det->product_name;
                    $productdetail['product_code'] = $varient_default_check->product_code;
                    $productdetail['var_product_name'] = $varient_default_check->product_name;
                    $productdetail['varaintnameids'] = $varient_default_check->varaintnameids;
                    $productdetail['slug'] = $dflt_prod_det->slug;
                    $productdetail['description'] = $dflt_prod_det->description;
                    $productdetail['how_to_prepare'] = $dflt_prod_det->how_to_prepare;
                    $productdetail['ingredients'] = $dflt_prod_det->ingredients;
                    $productdetail['quantity'] = $varient_default_check->variant_qty;
                    $productdetail['tax_inc_price'] = $varient_default_check->variant_price_tax_inc;
                    $productdetail['tax_excl_price'] = $varient_default_check->variant_price_tax_excl;
                    $productdetail['shipping_price'] = $dflt_prod_det->shipping_price;
                    $productdetail['max_quantity'] = $varient_default_check->variant_max_qty;
                    $productdetail['percent'] = $dflt_prod_det->percent;
                    $productdetail['active_status'] = $dflt_prod_det->active_status;

	                if($dflt_prod_det->tax_rule_id) {
	                	$tax_det = TaxRule::where('id',$dflt_prod_det->tax_rule_id)->where('active_status',1)->first();

	                	if($tax_det) {
	                		$productdetail['tax_det'] = $tax_det->rule_name;
	                	} else {
	                		$productdetail['tax_det'] = '';
	                	}
	                } else {
	                	$productdetail['tax_det'] = '';
	                }

                    $gallery = ProductCombinationGallery::where('product_id',$varient_default_check->product_id)->where('product_variant_id',$varient_default_check->id)->orderBy('priority','ASC')->get();

            } else {

                $varientdefaultcheck = AttributeCombination::where('product_id',$product_detail->id)->where('active_status',1)->orderBy('id','ASC')->limit(1)->first();

                if($varientdefaultcheck) {

                    $dflt_prod_det = Product::where('id',$varientdefaultcheck->product_id)->first();

                    $productdetail['id'] = $dflt_prod_det->id;
                    $productdetail['image'] = $varientdefaultcheck->image;
                    $productdetail['image_url'] = URL('/').'/product_combination_photos';
                    $productdetail['product_name'] = $dflt_prod_det->product_name;
                    $productdetail['product_code'] = $varientdefaultcheck->product_code;
                    $productdetail['var_product_name'] = $varientdefaultcheck->product_name;
                    $productdetail['varaintnameids'] = $varientdefaultcheck->varaintnameids;
                    $productdetail['slug'] = $dflt_prod_det->slug;
                    $productdetail['description'] = $dflt_prod_det->description;
                    $productdetail['how_to_prepare'] = $dflt_prod_det->how_to_prepare;
                    $productdetail['ingredients'] = $dflt_prod_det->ingredients;
                    $productdetail['quantity'] = $varientdefaultcheck->variant_qty;
                    $productdetail['tax_inc_price'] = $varientdefaultcheck->variant_price_tax_inc;
                    $productdetail['tax_excl_price'] = $varientdefaultcheck->variant_price_tax_excl;
                    $productdetail['shipping_price'] = $dflt_prod_det->shipping_price;
                    $productdetail['max_quantity'] = $varientdefaultcheck->variant_max_qty;
                    $productdetail['percent'] = $dflt_prod_det->percent;
                    $productdetail['active_status'] = $dflt_prod_det->active_status;

	                if($dflt_prod_det->tax_rule_id) {
	                	$tax_det = TaxRule::where('id',$dflt_prod_det->tax_rule_id)->where('active_status',1)->first();

	                	if($tax_det) {
	                		$productdetail['tax_det'] = $tax_det->rule_name;
	                	} else {
	                		$productdetail['tax_det'] = '';
	                	}
	                } else {
	                	$productdetail['tax_det'] = '';
	                }

                    $gallery = ProductCombinationGallery::where('product_id',$varientdefaultcheck->product_id)->where('product_variant_id',$varientdefaultcheck->id)->orderBy('priority','ASC')->get();

                }

            }

        } else {

            if($product_detail->combinations == 'No') {

                $productdetail['id'] = $product_detail->id;
                $productdetail['image'] = $product_detail->image;
                $productdetail['image_url'] = URL('/').'/product_photos';
                $productdetail['product_name'] = $product_detail->product_name;
                $productdetail['product_code'] = $product_detail->product_code;
                $productdetail['var_product_name'] = '';
                $productdetail['slug'] = $product_detail->slug;
                $productdetail['description'] = $product_detail->description;
                $productdetail['how_to_prepare'] = $product_detail->how_to_prepare;
                $productdetail['ingredients'] = $product_detail->ingredients;
                $productdetail['quantity'] = $product_detail->quantity;
                $productdetail['tax_inc_price'] = $product_detail->tax_inc_price;
                $productdetail['tax_excl_price'] = $product_detail->tax_excl_price;
                $productdetail['shipping_price'] = $product_detail->shipping_price;
                $productdetail['max_quantity'] = $product_detail->max_quantity;
                $productdetail['percent'] = $product_detail->percent;
                $productdetail['active_status'] = $product_detail->active_status;

                if($product_detail->tax_rule_id) {
                	$tax_det = TaxRule::where('id',$product_detail->tax_rule_id)->where('active_status',1)->first();

                	if($tax_det) {
                		$productdetail['tax_det'] = $tax_det->rule_name;
                	} else {
                		$productdetail['tax_det'] = '';
                	}
                } else {
                	$productdetail['tax_det'] = '';
                }

                $gallery = ProductGallery::where('product_id',$product_detail->id)->orderBy('priority','ASC')->get();

            }

        }
        
        $productdetail = (Object)$productdetail;
        $product_detail = $productdetail;


		/********** End New **********/



		// $gallery = Product::with('gallery')->where('id',$product_detail->id)->where('active_status',1)->first();

		$prodcategories = ProductCategory::where('product_id',$product_detail->id)->get();

		$prodcatarray = array();

		foreach($prodcategories as $cat) {
			array_push($prodcatarray, $cat->category_id);
		}

		// return $gallery->gallery;

		$gallery_array = array();
		// foreach($gallery->gallery as $galler) {

		// 	array_push($gallery_array, $galler->image);
		// }
		// array_unshift($gallery_array,"");
		// unset($gallery_array[0]);
		// $feature_status = 0;

		$productvariants = AttributeCombination::where('product_id',$product_detail->id)
												->where('productvariants.active_status',1)
												->get();

		$productdetailforview = array();
		$availableattributes = array();




		//second testing code

	$productdetails = ProductDetailValue::join('productvariants','product_details.product_variant_id','=','productvariants.id')
										->join('attribute_values','product_details.value_id','=','attribute_values.id')
										->join('attributes','attribute_values.attribute_id','=','attributes.id')
										->where('productvariants.product_id','=',$product_detail->id)
										->where('productvariants.active_status',1)
										->select('product_details.*','productvariants.variantname','attribute_values.attribute_id','product_details.value_id','attributes.name','attribute_values.attribute_value','productvariants.product_id')
										->orderBy('product_details.id','ASC')
										->get();


		foreach($productdetails as $det) {

				if(in_array($det->attribute_id, $availableattributes)) {


				} else {


					array_push($availableattributes, $det->attribute_id);
				}


			}


			$makeviewarray = array();

			for($i=0;$i<sizeof($availableattributes); $i++) {


				$getattrvalues = AttributeValue::join('product_details','attribute_values.id','=','product_details.value_id')
												->join('productvariants','productvariants.id','=','product_details.product_variant_id')
												->where('attribute_values.attribute_id',$availableattributes[$i])
												->where('attribute_values.active_status',1)
												->select('attribute_values.attribute_value','product_details.product_variant_id','attribute_values.id')
												->groupBy('attribute_values.attribute_value')
												->where('productvariants.product_id',$product_detail->id)
												->get();

				$dataget['attribute_id'] = $availableattributes[$i];
				$dataget['attribute_name'] = Attribute::where('id',$availableattributes[$i])->first();
				$dataget['attributevalues'] = $getattrvalues;

				array_push($makeviewarray, $dataget);
			}

			$makeviewcount = sizeof($makeviewarray);




		$product_id = $product_detail->id;

		$get_rate_stats = $this->getProductReviewStat($product_id);
		$all_reviews = $this->getAllReviews($product_id);



		$ratedata['average_rate'] =  $get_rate_stats['averagerate'];
		$ratedata['no_of_rates'] =  $get_rate_stats['no_of_rate'];
		$ratedata['all_reviews'] =  $all_reviews;


		if(sizeof($productvariants) > 0) {
			$feature_status = 1;


			$loginStatus = 'App\User'::checkMyLogin();

        if($loginStatus == 'success') {

			$check_review = $this->checkUserReview($product_id);

			if($check_review) {


				$review_status = $check_review;
			} else {

				$review_status = '';
			}

		} else {

			$review_status = '';
		}


		} else {
			$feature_status = 0;

			$loginStatus = 'App\User'::checkMyLogin();

      if($loginStatus == 'success') {


			$check_review = $this->checkUserReview($product_id);

			if($check_review) {


				$review_status = $check_review;
			} else {

				$review_status = '';
			}

				$get_data = $this->getCartByProductId($product_detail->id, Auth::user()->id);

				if($get_data) {

					$qty_exist = $get_data->quantity;

				} else {

					$qty_exist = 0;
				}

				// $max_quantity = $product_features->max_quantity - $qty_exist;

				if($product_detail->quantity >= $product_detail->max_quantity) {

					$max_quantity = $product_detail->max_quantity - $qty_exist;

				} else {

				    $max_quantity = $product_detail->quantity;

				}

			}

             else {

				if(Session::has('cart')) {
				$cart = session()->get('cart');

				if(isset($cart[$product_id]['quantity'])) {
					$qty_exist = $cart[$product_id]['quantity'];
				} else {
					$qty_exist = 0;
				}

				// $max_quantity = $product_features->max_quantity - $qty_exist;

				if($product_detail->quantity >= $product_detail->max_quantity) {

					$max_quantity = $product_detail->max_quantity - $qty_exist;

					} else {

						$max_quantity = $product_detail->quantity;

					}





				} else {
					$cart = '';


					if($product_detail->quantity >= $product_detail->max_quantity) {

					$max_quantity = $product_detail->max_quantity;

					} else {

						$max_quantity = $product_detail->quantity;

					}

				}

		}

		}

		$related_products = Product::test_related_products($product_id,$prodcatarray);

		if ($request->ajax()) {
            return view('userfiles.includes.reviewdata', compact('all_reviews'));
        }


        $most_viewed_product_check = Mostviewedproducts::where('product_id',$product_id)->first();

        if($most_viewed_product_check) {
        	Mostviewedproducts::where('product_id',$product_id)->update(['count'=>$most_viewed_product_check->count+1]);
        } else {
        	Mostviewedproducts::insert(['product_id'=>$product_id]);
        }


		return view('userfiles.productdetail',compact('product_detail','gallery_array','gallery','product_features','product_id','feature_status','max_quantity','review_status','ratedata','related_products','makeviewcount','allattributes','makeviewarray','all_reviews'));

		} else {
			return redirect('/');
		}
	}

	public function getProductPriceOnChangeAttribute($product_id,$attribute_id) {

		if($product_id && $attribute_id) {
			$get_det  = ProductAttribute::where('product_id',$product_id)
										->where('id',$attribute_id)
										->where('active_status',1)
										->first();

			if($get_det) {

				$data['stock_val'] = $get_det->stock;


				$data['max_quantity'] = $get_det->max_quantity;

				$loginStatus = 'App\User'::checkMyLogin();

	      if($loginStatus == 'success') {

				$get_data = $this->getCartByFeatureId($product_id,$attribute_id,Auth::user()->id);

				if($get_data) {

					$qty_exist = $get_data->feature_qty;

				} else {

					$qty_exist = 0;
				}

				if($get_det->stock > $get_det->max_quantity) {

					$data['max_quantity'] = $get_det->max_quantity - $qty_exist;

				} else {

					$data['max_quantity'] = $get_det->stock - $qty_exist;

				}



			} else {

				if(Session::has('cart')) {
				$cart = session()->get('cart');
				if(isset($cart[$product_id]['feature_items'][$attribute_id]['feature_qty'])) {
				$qty_exist = $cart[$product_id]['feature_items'][$attribute_id]['feature_qty'];
				} else {
				$qty_exist = 0;
				}

				if($get_det->stock > $get_det->max_quantity) {

					$data['max_quantity'] = $get_det->max_quantity - $qty_exist;

				} else {

					$data['max_quantity'] = $get_det->stock - $qty_exist;

				}


				// $data['max_quantity'] = $get_det->max_quantity - $qty_exist;

				} else {
				 $cart = '';

				 if($get_det->stock > $get_det->max_quantity) {

					$data['max_quantity'] = $get_det->max_quantity;

				} else {

					$data['max_quantity'] = $get_det->stock;

				}

				}

				}




				$data['price_tax_inc'] = $get_det->price_tax_inc;
				// $data['max_quantity'] = $get_det->max_quantity;
				return json_encode(array('status'=>200,'info'=>$get_det,'quantity'=>$data['max_quantity']));
			} else {
				return json_encode(array('status'=>500,'info'=>'no product found'));
			}
		} else {
			return json_encode(array('status'=>500,'info'=>'something wrong.'));
		}
	}

	public function getProductPriceOnChangeQuantity($product_id,$quantity,$attribute_id) {

		if($product_id && $quantity && $attribute_id) {

			if($quantity <=0) {
				return -1;
			}

			$product_features_count = Product::with('features')->where('id',$product_id)->first();

			if(sizeof($product_features_count->features) > 0) {

				$get_det  = ProductAttribute::where('product_id',$product_id)
										->where('id',$attribute_id)
										->where('active_status',1)
										->first();

				if($get_det) {

				$attribute_price = $get_det->price_tax_inc;
				$attribute_price_final = number_format($attribute_price * $quantity, 2);

				return $attribute_price_final;

				} else {
					return -1;
				}



			} else {
				return -1;
			}



		} else {
			return -1;
		}


	}

	public function getProductPriceGlobalChangeQuantity($product_id,$quantity) {

		if($product_id && $quantity) {

			if($quantity <=0) {
				return -1;
			}

		$product_features_count = Product::with('features')->where('id',$product_id)->first();

		if(sizeof($product_features_count->features) > 0) {

					return -1;

			} else {
				$get_det  = Product::where('id',$product_id)
										->where('active_status',1)
										->first();

				if($get_det) {

				$price = $get_det->tax_inc_price;
				$price_final = number_format($price * $quantity);

				return $price_final;

				} else {
					return -1;
				}
			}



		} else {

			return -1;
		}

	}

	public function getCheckLogin() {

		if(Auth::check() && Auth::user()->role_id == 2) {

			return 1;

		} else {

			return 0;
		}
	}


	public function getCmsPage($slug) {
		$get_cms = Cms::where('slug','=',$slug)->first();



		if($get_cms) {

			return view('userfiles.cmspage',compact('get_cms'));

		} else {
			return redirect('/');
		}

	}

	public function getContactPage() {

		return view('userfiles.contact');
	}

	public function getBlog() {

		$blogs = Blog::where('active_status',1)->get();

		return view('userfiles.blog',compact('blogs'));
	}

	public function getBlogDetail($slug) {

		if($slug) {
			$get_blog = Blog::where('slug','=',$slug)->first();

			if($get_blog) {

				return view('userfiles.blog_detail', compact('get_blog'));

			} else {

				return redirect('blog');

			}

		} else {

			return redirect('blog');
		}
	}


	// public function getAllProducts() {

	// 		if(isset($_GET['pricefilter'])) {

	// 			$price_filter = $_GET['pricefilter'];
	// 		} else {

	// 			$price_filter = "none";
	// 		}

	// 				// return $related_categories;
	// 			$product_detailarr = array();

	// 			$products = Product::where('active_status',1);

	// 			if($price_filter =="none") {

	// 				$products = $products->orderBy('id','DESC')->get();

	// 			} else {

	// 				if($price_filter == "pricelow") {

	// 				$products = $products->orderBy('tax_inc_price','ASC');

	// 				} else if($price_filter == "pricehigh") {

	// 				$products = $products->orderBy('tax_inc_price','DESC');
	// 				} else {
	// 				$products = $products->orderBy('id','DESC');

	// 				}


	// 				$products = $products->get();
	// 			}


	// 			foreach($products as $prod) {

	// 				$prodresponse['id'] = $prod->id;
	// 				$prodresponse['image'] = $prod->image;
	// 				$prodresponse['product_name'] = $prod->product_name;
	// 				$prodresponse['slug'] = $prod->slug;
	// 				$prodresponse['description'] = $prod->description;
	// 				$prodresponse['quantity'] = $prod->quantity;
	// 				$prodresponse['tax_inc_price'] = $prod->tax_inc_price;
	// 				$prodresponse['tax_excl_price'] = $prod->tax_excl_price;
	// 				$prodresponse['price_from'] = $prod->price_from;
	// 				$prodresponse['price_to'] = $prod->price_to;
	// 				$prodresponse['shipping_price'] = $prod->shipping_price;
	// 				$prodresponse['product_code'] = $prod->product_code;
	// 				$prodresponse['max_quantity'] = $prod->max_quantity;
	// 				$prodresponse['percent'] = $prod->percent;
	// 				$prodresponse['active_status'] = $prod->active_status;

	// 				$reviewstat = $this->getProductReviewStat($prod->id);
	// 				$prodresponse['avg_rate'] = $reviewstat['averagerate'];

	// 				array_push($product_detailarr,$prodresponse);

	// 			}

	// 			// return $product_detailarr;
	// 			return view('userfiles.allproducts',compact('product_detailarr'));


	// }

	public function getSpecials() {

		if(isset($_GET['pricefilter'])) {

				$price_filter = $_GET['pricefilter'];
			} else {

				$price_filter = "none";
			}

					// return $related_categories;
				$product_detailarr = array();

				$products = Product::where('active_status',1)->where('percent','>',0);

				if($price_filter =="none") {

					$products = $products->orderBy('id','DESC')->get();

				} else {

					if($price_filter == "pricelow") {

					$products = $products->orderBy('tax_inc_price','ASC');

					} else if($price_filter == "pricehigh") {

					$products = $products->orderBy('tax_inc_price','DESC');
					} else {
					$products = $products->orderBy('id','DESC');

					}


					$products = $products->get();
				}


				foreach($products as $prod) {

					$prodresponse['id'] = $prod->id;
					$prodresponse['image'] = $prod->image;
					$prodresponse['product_name'] = $prod->product_name;
					$prodresponse['slug'] = $prod->slug;
					$prodresponse['description'] = $prod->description;
					$prodresponse['quantity'] = $prod->quantity;
					$prodresponse['tax_inc_price'] = $prod->tax_inc_price;
					$prodresponse['tax_excl_price'] = $prod->tax_excl_price;
					$prodresponse['price_from'] = $prod->price_from;
					$prodresponse['price_to'] = $prod->price_to;
					$prodresponse['shipping_price'] = $prod->shipping_price;
					$prodresponse['product_code'] = $prod->product_code;
					$prodresponse['max_quantity'] = $prod->max_quantity;
					$prodresponse['percent'] = $prod->percent;
					$prodresponse['active_status'] = $prod->active_status;

					$reviewstat = $this->getProductReviewStat($prod->id);
					$prodresponse['avg_rate'] = $reviewstat['averagerate'];

					array_push($product_detailarr,$prodresponse);

				}

				// return $product_detailarr;
				return view('userfiles.specials',compact('product_detailarr'));

	}


	public function postProductSearch(Request $request) {

		$search = $request->productname;

		if(isset($_GET['pricefilter'])) {

				$price_filter = $_GET['pricefilter'];
			} else {

				$price_filter = "none";
			}

					// return $related_categories;
				$product_detailarr = array();

				$products = Product::where('active_status',1)->where('product_name','LIKE','%'.$search.'%');

				if($price_filter =="none") {

					$products = $products->orderBy('id','DESC')->get();

				} else {

					if($price_filter == "pricelow") {

					$products = $products->orderBy('tax_inc_price','ASC');

					} else if($price_filter == "pricehigh") {

					$products = $products->orderBy('tax_inc_price','DESC');
					} else {
					$products = $products->orderBy('id','DESC');

					}


					$products = $products->get();
				}


				foreach($products as $prod) {

					$prodresponse['id'] = $prod->id;
					$prodresponse['image'] = $prod->image;
					$prodresponse['product_name'] = $prod->product_name;
					$prodresponse['slug'] = $prod->slug;
					$prodresponse['description'] = $prod->description;
					$prodresponse['quantity'] = $prod->quantity;
					$prodresponse['tax_inc_price'] = $prod->tax_inc_price;
					$prodresponse['tax_excl_price'] = $prod->tax_excl_price;
					$prodresponse['price_from'] = $prod->price_from;
					$prodresponse['price_to'] = $prod->price_to;
					$prodresponse['shipping_price'] = $prod->shipping_price;
					$prodresponse['product_code'] = $prod->product_code;
					$prodresponse['max_quantity'] = $prod->max_quantity;
					$prodresponse['percent'] = $prod->percent;
					$prodresponse['active_status'] = $prod->active_status;

					$reviewstat = $this->getProductReviewStat($prod->id);
					$prodresponse['avg_rate'] = $reviewstat['averagerate'];

					array_push($product_detailarr,$prodresponse);

				}

				// return $product_detailarr;
				return view('userfiles.searchproducts',compact('product_detailarr'));
	}

	public function postAttributePrice(Request $request) {


		$attrslist = $request->myfield;
		$product_id = $request->product_id;
		$quantity = $request->quantity;
		// return $attrslist;

		$combine_array = array();

		foreach($attrslist as $att) {

			if($att['value']!=0) {

				array_push($combine_array, $att['value']);
			}

		}

		$combine_format =  implode('-', $combine_array);

		// return $combine_format;


		$get_combination_data = $this->checkCombinationPrice($combine_format, $product_id);



		if($get_combination_data == "input_error") {

			return json_encode(array('status'=>500,'info'=>'Attribute parameter missing.'));
		}

		if($get_combination_data == "combination_not_found") {

			return json_encode(array('status'=>500,'info'=>'Selected Combination Not found.'));
		}



		$get_quantity_data = $this->getProductPriceOnChangeAttributeTrait($product_id,$combine_format,$quantity);

		$get_combination_images = ProductCombinationGallery::where('product_variant_id',$get_combination_data->id)->get();


		return json_encode(array('status'=>200,'info'=>$get_combination_data,'info1'=>$get_quantity_data,'quantity'=>$quantity,'combination_images'=>$get_combination_images));

	}





	public function pc_permute($items, $perms = array( )) {

		    $back = array();
		    if (empty($items)) {
		        $back[] = join(' ', $perms);
		    } else {
		        for ($i = count($items) - 1; $i >= 0; --$i) {
		             $newitems = $items;
		             $newperms = $perms;
		             list($foo) = array_splice($newitems, $i, 1);
		             array_unshift($newperms, $foo);
		             $back = array_merge($back, $this->pc_permute($newitems, $newperms));
		         }
		    }
		    return $back;
	}



	public function postProductWithoutAttributePrice(Request $request) {

		$product_id = $request->product_id;
		$quantity = $request->quantity;


		$get_product_data = $this->getProductDetailData($product_id);

		if($get_product_data) {

				$product_price = $get_product_data->tax_excl_price;
		    	$quantity_price = number_format($product_price * $quantity);
				$offer_quantity_price = number_format($this->getOfferPrice($product_price,$product_id,$quantity));

				return json_encode(array('status'=>200,'info'=>$quantity_price,'info1'=>$offer_quantity_price));



		} else {

			return json_encode(array('status'=>500,'info'=>'Selected Product Not found.'));
		}


	}

	public function postApplyCoupon(Request $request) {

		$coupon = Offer::where('name',$request->coupon_code)->where('active_status',1)->first();

		if(!$coupon) {

			return json_encode(array('status'=>500,'info'=>'Entered Coupon is not valid.'));
		}

		$loginStatus = 'App\User'::checkMyLogin();

		if($loginStatus == "success") {

			$checktoken = $this->checkCoupon($coupon->id, Auth::user()->id);

		} else {

			$checktoken = $this->checkCoupon($coupon->id);
		}



		if($checktoken == 'coupon_not_found') {

			return json_encode(array('status'=>500,'info'=>'Entered Coupon is not valid.'));
		} else if($checktoken == 'already_avail') {

			return json_encode(array('status'=>500,'info'=>'This coupon is not valid as you have already used this.'));
		} else {

			session()->put('coupon', [
			'couponid' => $coupon->id,
			'name' => $coupon->name,
			'discount' => $coupon->discount(),

		]);

		return json_encode(array('status'=>200,'info'=>'Coupon has been Applied successfully.'));

		}


	}


	public function postRemoveCoupon(Request $request) {

		session()->forget('coupon');

		return json_encode(array('status'=>200,'info'=>'Coupon has been Removed successfully.'));

	}



	public function postAddToWishlist(Request $request) {


		$loginStatus = 'App\User'::checkMyLogin();

			if($loginStatus == 'success') {

			$productdet = $this->getProductBySingeSlug($request->slug,Auth::user()->id);


			if($productdet == "already_present") {

			return json_encode(array('status'=>500,'info'=>'Selected Product already present in the wishlist.'));

			} else if($productdet == "product_not_found") {

			return json_encode(array('status'=>500,'info'=>'Selected Product Not found.'));

			} else {

				$wish = new WishList;
				$wish->user_id = Auth::user()->id;
				$wish->product_id = $productdet->id;

				if($wish->save()) {

				return json_encode(array('status'=>200,'info'=>'You have successfully added this product to your wishlist.'));

				} else {

				return json_encode(array('status'=>500,'info'=>'Error while adding to wishlist. try again.'));

				}
			}

		} else {

			return json_encode(array('status'=>600,'info'=>'Login first to add for wishlist'));
		}
	}

	public function getWishLists() {

		$loginStatus = 'App\User'::checkMyLogin();

      if($loginStatus == 'success') {

			$wishlistsdata = User::with(['wishlist' => function($query) {

				$query->join('products','wishlists.product_id','=','products.id')
					  ->where('products.active_status',1)
					  ->select('wishlists.*','products.product_name','products.slug','products.image');

			}])->where('users.id',Auth::user()->id)->first();

			// return $wishlistsdata->wishlists;

			return view('userfiles.wishlist',compact('wishlistsdata'));


		} else {

			$redirecturl = url('my-wishlists');

    	 	return redirect('/login?redirectto='.$redirecturl);
		}
	}


	public function deleteWishItem($wish_id) {


		$loginStatus = 'App\User'::checkMyLogin();

    if($loginStatus == 'success') {

			$deletedata = WishList::where('id',$wish_id)->delete();

			if($deletedata) {

			return json_encode(array('status'=>200,'info'=>'Item has been deleted successfully from wishlist.'));

			} else {

				return json_encode(array('status'=>500,'info'=>'Error while deleting. try again.'));

			}

		} else {

			return json_encode(array('status'=>600,'info'=>'Login first to add for wishlist'));
		}

	}

	public function getTestSms() {

		return false;
	}



	public function ChangeListProductVariant(Request $request)
    {

      $product_id = $request->proid;
      $product_variant_id = $request->provarid;
      $product_variant_qty = $request->provarqty; 

      $check_status = AttributeCombination::where('id',$product_variant_id)->where('product_id','=',$product_id)->where('active_status',1)->first();

      if($check_status) {

        if($product_variant_qty > $check_status->variant_qty) {
        	$response['result'] = 0;
        	$response['error_msg'] = "Insufficient quantity available. Only ".$check_status->variant_qty." number of quantities are available.";
        	return response()->json($response);
        }

        $prod_det = Product::where('id',$product_id)->first();
        $tax_rule_det = TaxRule::where('id',$prod_det->tax_rule_id)->first();

        $offer_price = Product::getCalculateOffer($check_status->variant_price_tax_excl,$prod_det->percent);
        $price = $check_status->variant_price_tax_excl;

        $response['result'] = 1;
        $response['offer_price'] = $offer_price*$product_variant_qty;
        $response['price'] = $price*$product_variant_qty;
        $response['offer_prnct'] = $prod_det->percent;

        return response()->json($response);

      } else {

      	$prod_det = $prod_det = Product::where('id',$product_id)->first();

      	if($prod_det) {

	        $offer_price = Product::getCalculateOffer($prod_det->tax_excl_price,$prod_det->percent);
	        $price = $check_status->variant_price_tax_excl;

	        $response['result'] = 1;
	        $response['offer_price'] = $offer_price*$product_variant_qty;
	        $response['price'] = $price*$product_variant_qty;
	        $response['offer_prnct'] = $prod_det->percent;

	        return response()->json($response);

      	} else {
      		$response['result'] = 0;
	        $response['provarid'] = $product_variant_id;
	        $response['error_msg'] = "Result not found.";
	        $response['offer_prnct'] = 0;
	        return response()->json($response);
      	}
        
      }

      $response['result'] = 0;
      $response['provarid'] = $product_variant_id;
      $response['error_msg'] = "Something went wrong.";
      $response['offer_prnct'] = 0;
      return response()->json($response);

    }


    public function ChangeListProductQuantity(Request $request)
    {

      $product_id = $request->proid;
      $product_variant_id = $request->provarid;
      $product_variant_qty = $request->provarqty; 

        if($product_variant_id > 0) {

		    $check_status = AttributeCombination::where('id',$product_variant_id)->where('product_id','=',$product_id)->where('active_status',1)->first();

		    if($check_status) {

		        if($product_variant_qty > $check_status->variant_qty) {
		        	$response['result'] = 0;
		        	$response['error_msg'] = "Insufficient quantity available. Only ".$check_status->variant_qty." number of quantities are available.";
		        	return response()->json($response);
		        }

		        $prod_det = Product::where('id',$product_id)->first();
		        $tax_rule_det = TaxRule::where('id',$prod_det->tax_rule_id)->first();

		        $offer_price = Product::getCalculateOffer($check_status->variant_price_tax_excl,$prod_det->percent);
		        $price = $check_status->variant_price_tax_excl;

		        $response['result'] = 1;
		        $response['offer_price'] = $offer_price*$product_variant_qty;
		        $response['price'] = $price*$product_variant_qty;
		        $response['offer_prnct'] = $prod_det->percent;

		        return response()->json($response);

		    } else {

		    	$prod_det = Product::where('id',$product_id)->first();

		    	if($prod_det) {

		    		$prod_det = Product::where('id',$product_id)->first();
			        $tax_rule_det = TaxRule::where('id',$prod_det->tax_rule_id)->first();

			        $offer_price = Product::getCalculateOffer($prod_det->tax_excl_price,$prod_det->percent);
			        $price = $check_status->tax_excl_price;

			        $response['result'] = 1;
			        $response['offer_price'] = $offer_price*$product_variant_qty;
			        $response['price'] = $price*$product_variant_qty;
			        $response['offer_prnct'] = $prod_det->percent;
			        return response()->json($response);

		    	} else {

		    		$response['result'] = 0;
			        $response['provarid'] = $product_variant_id;
			        $response['error_msg'] = "Result not found.";
			        $response['offer_prnct'] = 0;
			        return response()->json($response);

		    	}

		    	$response['result'] = 0;
		        $response['provarid'] = $product_variant_id;
		        $response['error_msg'] = "Result not found.";
		        $response['offer_prnct'] = 0;
		        return response()->json($response);
		        
		    }

		} else {

			$prod_det = Product::where('id',$product_id)->where('active_status',1)->first();

			if($prod_det) {

				if($product_variant_qty > $prod_det->quantity) {
		        	$response['result'] = 0;
		        	$response['error_msg'] = "Insufficient quantity available. Only ".$prod_det->quantity." number of quantities are available.";
		        	return response()->json($response);
		        }

		        $tax_rule_det = TaxRule::where('id',$prod_det->tax_rule_id)->first();

		        $offer_price = Product::getCalculateOffer($prod_det->tax_excl_price,$prod_det->percent);
		        $price = $prod_det->tax_excl_price;

		        $response['result'] = 1;
		        $response['offer_price'] = $offer_price*$product_variant_qty;
		        $response['price'] = $price*$product_variant_qty;
		        $response['offer_prnct'] = $prod_det->percent;

		        return response()->json($response);

		    } else {

		        $response['result'] = 0;
		        $response['provarid'] = $product_variant_id;
		        $response['error_msg'] = "Result not found.";
		        $response['offer_prnct'] = 0;
		        return response()->json($response);

		    }

		}

      $response['result'] = 0;
      $response['provarid'] = $product_variant_id;
      $response['error_msg'] = "Something went wrong.";
      $response['offer_prnct'] = 0;
      return response()->json($response);

    }






}



?>
