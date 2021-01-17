<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });
if(version_compare(PHP_VERSION, '7.2.0', '>=')) {
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
}

Route::get('clear-cache', function() {
	return Artisan::call('cache:clear');
});

Route::get('config-cache', function() {
	return Artisan::call('config:cache');
});

Route::get('test-code', function() {

  return $better_token = md5(uniqid(rand(), true));;
  // return view('adminfiles.testcode');
});

Route::get('test-order/{orderid}','OrderController@testOrderId');
Route::get('404','WebsiteController@errorpage');

//website routes

Route::get('/','WebsiteController@getHomePage');
Route::get('about-us','WebsiteController@getAboutusPage');
Route::get('terms-conditions','WebsiteController@getermsConditionsPage');
Route::get('privacy-policy','WebsiteController@getPrivacyPolicyPage');
Route::get('test-sms','WebsiteController@getTestSms');
Route::get('category/{categoryslug}','WebsiteController@getProductPage');
Route::get('product/{slug}','WebsiteController@getProductDetail');
Route::get('products','WebsiteController@getAllProducts');

// Change List Product Variant
Route::post('change-product-variant','WebsiteController@ChangeListProductVariant');
Route::post('change-product-quantity','WebsiteController@ChangeListProductQuantity');
Route::post('list-product-add-to-cart','CartController@postListProductAddToCart');

//new routes
Route::post('attribute-price','WebsiteController@postAttributePrice');
Route::post('product-withoutattribute-price','WebsiteController@postProductWithoutAttributePrice');

Route::get('product-price-on-change-size-attribute/{product_id}/{attribute_id}','WebsiteController@getProductPriceOnChangeAttribute');
Route::get('product-price-on-change-quantity/{product_id}/{quantity}/{attr_id}','WebsiteController@getProductPriceOnChangeQuantity');

Route::post('fetch-search-data','WebsiteController@postSearchData');
Route::get('specials','WebsiteController@getSpecials');
Route::any('product-search','WebsiteController@postProductSearch');
Route::post('apply-coupon','WebsiteController@postApplyCoupon');
Route::post('remove-coupon','WebsiteController@postRemoveCoupon');

Route::get('product-price-global-change-quantity/{product_id}/{quantity}','WebsiteController@getProductPriceGlobalChangeQuantity');
Route::post('add-to-cart','CartController@postAddToCart');
Route::get('cart','CartController@getCart');
Route::get('change-quantity-val/{product_id}/{feature_id}','CartController@getChangeQuantityVal');
Route::get('cart-count','CartController@getCartCount');
Route::get('delete-cart-item/{product_id}/{feature_id?}','CartController@deleteCartItem');
Route::get('delete-cart-item-feature/{product_id}/{feature_id}','CartController@deleteCartItemFeature');

Route::get('change-cart-quantity-global/{product_id}/{sel_val}','CartController@getChangeCartQuantity');
// Route::get('change-cart-quantity-feature/{product_id}/{feature_id}/{sel_val}','CartController@getChangeCartQuantityFeature');

Route::post('change-cart-quantity-feature','CartController@postChangeCartQuantityFeature');
Route::post('change-cart-quantity-base','CartController@postChangeCartQuantityBase');

/******** checkout routes **********/
Route::get('check-items-before-checkout','CartController@checkCartBeforeCheckout');
Route::get('checkout','CheckoutController@getCheckoutPage');
Route::post('add-address','CheckoutController@postAddAddress');
Route::post('update-address','CheckoutController@postUpdateAddress');
Route::post('delete-address','CheckoutController@postDeleteAddress');
Route::post('add-billing-address','CheckoutController@postAddBillingAddress');
Route::post('update-billing-address','CheckoutController@postUpdateBillingAddress');
Route::post('delete-billing-address','CheckoutController@postDeleteBillingAddress');


Route::post('place-order','OrderController@postPlaceOrder');
Route::post('webhook','OrderController@post_webhook_payment');
Route::any('payment-status','OrderController@postThankYouPage');
Route::get('payment-result','OrderController@getThankYouPage');
Route::get('payment-cancel','OrderController@getCancelPage');

Route::get('reorder/{token}','OrderController@getReorder');

Route::post('add-review','UserReviewController@postAddReview');
Route::get('check-user-login','WebsiteController@getCheckLogin');
Route::get('page/{slug}','WebsiteController@getCmsPage');
Route::get('contact-us','WebsiteController@getContactPage');
Route::post('post-contact-us','WebsiteController@postContactUs');
Route::get('blogs','WebsiteController@getBlog');
Route::get('blog-detail/{slug}','WebsiteController@getBlogDetail');

/********** Login and register routes ***********/
Route::get('login','UserController@getUserLogin');
Route::post('login','UserController@postUserLogin');
Route::get('register','UserController@getUserRegister');
Route::post('register','UserController@postUserRegister');
Route::get('otp/{token}','UserController@getOtpPage');
Route::post('otp/{token}','UserController@postOtpPage');
Route::get('resend-otp/{token}','UserController@getResendOtp');
Route::get('logout','UserController@getLogout');
Route::get('forgot-password','UserController@getForgotpassword');
Route::post('forgot-password','UserController@postForgotpassword');
Route::get('reset-password/{token}','UserController@getResetpassword');
Route::post('reset-password/{token}','UserController@postResetpassword');

/********* Wishlist routes ***********/
ROute::post('add-to-wishlist','WebsiteController@postAddToWishlist');
Route::get('my-wishlists','WebsiteController@getWishLists');
Route::get('delete-wishlist-item/{id}','WebsiteController@deleteWishItem');

Route::get('orders-history','UserController@getUserHistory');
Route::get('order-product-details/{token}','UserController@getOrderDetails');
Route::post('cancel-order','UserController@postOrderCancel');
Route::get('track-order/{token}','UserController@getTrackMyOrder');

Route::get('my-profile','UserController@getMyProfile');
Route::post('update-profile','UserController@postUpdateProfile');
Route::any('profile-change-password','UserController@postProfileChangePassword');
Route::post('update-profile-change-password','UserController@postUpdateProfileChangePassword');

Route::get('adminlogin','LoginController@showAdminLoginForm');
Route::post('adminlogin','LoginController@adminLogin');
Route::get('adminlogout','LoginController@adminLogout');

Route::get('adminhome','AdminController@showHomePage');
Route::get('admin/change-status/{table}/{status}/{id}','AdminController@change_status');
Route::get('admin/delete-data/{id}/{table}','AdminController@delete_data');


Route::group(['prefix' => 'admin'], function(){

Route::get('category-manage','CategoryController@getCategoryManage');
Route::get('category-crud-add','CategoryController@getCategoryCrud');
Route::get('category-crud-add-updated','CategoryController@getCategoryCrudUpdated');
Route::post('category-crud-add','CategoryController@postCategoryCrud');
Route::get('category-crud-update/{id}','CategoryController@getCategoryUpdate');
Route::post('category-crud-update/{id}','CategoryController@postCategoryUpdate');


Route::get('subcategory-crud-add','CategoryController@getSubCategoryCrud');
Route::post('subcategory-crud-add','CategoryController@postSubCategoryCrud');
Route::get('subcategory-crud-update/{id}','CategoryController@getSubCategoryUpdate');
Route::post('subcategory-crud-update/{id}','CategoryController@postSubCategoryUpdate');

Route::post('change-cat-slug','CategoryController@change_cat_slug');


//taxrule routes
Route::get('tax-rule-manage','TaxrateController@getTaxRulePage');
Route::post('tax-rule-manage','TaxrateController@postTaxRulePage');
Route::get('tax-crud-update/{id}','TaxrateController@getTaxCrudUpdate');
Route::post('tax-crud-update/{id}','TaxrateController@postTaxCrudUpdate');

//products routess
Route::get('products-manage','ProductController@getProductManage');
Route::post('products-manage','ProductController@postProductManage');
Route::get('get-tax-rate/{id}','ProductController@get_tax_rate');
Route::get('product-without-combination-update/{id}','ProductController@getProductUpdatePage');
Route::post('product-without-combination-update/{id}','ProductController@postProductUpdatePage');
Route::get('product-with-combination-update/{id}','ProductController@getProductWithCombinationUpdatePage');
Route::post('product-with-combination-update/{id}','ProductController@postProductUpdatePage');
// Route::get('make-feature/{id}','ProductController@getMakeFeature');
Route::get('make-feature/{table}/{status}/{id}','ProductController@getMakeFeature');

//features and feature values
Route::get('features-manage','ProductController@getFeatureManage');
Route::post('features-manage','ProductController@postFeatureManage');
Route::post('features-manage-values','ProductController@postFeatureManageValues');
Route::get('get-feature-values/{id}','ProductController@getFeatureValues');
Route::get('products-attribute-edit/{id}','ProductController@getProductAttributeEdit');
Route::post('products-attribute-edit/{id}','ProductController@postProductAttributeEdit');



//dynamic attributes and combinations
Route::get('products-attribute-combination-edit/{id}','ProductController@getProductAttributeCombinationEdit');
Route::post('products-attribute-combination-edit/{id}','ProductController@postProductAttributeCombinationEdit');

Route::get('product-delete-attribute-combination/{id}','ProductController@deleteAttributeCombination');
Route::get('product-edit-attribute-combination/{id}','ProductController@getAttributeeditCombination');
Route::post('product-edit-attribute-combination/{id}','ProductController@postAttributeeditCombination');

Route::get('product-combination-make-default/{table}/{status}/{id}','ProductController@getProductCombinationMakeDefault');


// Product Combination gallery
Route::get('products-combination-gallery/{id}','ProductController@getProductCombinationGallery');
Route::post('products-combination-gallery/{id}','ProductController@postProductCombinationGallery');
Route::get('product-combination-gallery-delete/{id}','ProductController@deleteProductCombinationGallery');
Route::post('product-combination-image-priority-update','ProductController@updateProductCombinationGalleryPriority');


//gallery routes
Route::get('products-gallery/{id}','ProductController@getProductGallery');
Route::post('products-gallery/{id}','ProductController@postProductGallery');
Route::get('product-gallery-delete/{id}','ProductController@deleteProductGallery');

//slider routes
Route::get('sliders','SliderController@getSliderPage');
Route::post('sliders','SliderController@postSliderPage');
Route::get('slider-delete/{id}','SliderController@deleteSlider');
// Route::get('product-gallery-delete/{id}','ProductController@deleteProductGallery');


//offers routes
Route::get('offers','OfferController@getOfferPage');
Route::post('offers','OfferController@postOfferPage');
Route::get('offer-update/{id}','OfferController@getOfferUpdatePage');
Route::post('offer-update/{id}','OfferController@postOfferUpdatePage');

//users Routes
Route::get('all-users','AdminUserController@getUsersPage');
Route::post('all-users','AdminUserController@postUsersPage');
Route::post('user-update/{id}','AdminUserController@postUserUpdatePage');
Route::get('manage-address/{id}','AdminUserController@getManageAddressPage');
Route::post('manage-address/{id}','AdminUserController@postManageAddressPage');
Route::post('update-address/{id}','AdminUserController@postUpdateAddressPage');


//Blog routes
Route::get('blog-manage','AdminBlogController@getAddBlog');
Route::post('blog-manage','AdminBlogController@postAddBlog');
Route::get('blog-update/{id}','AdminBlogController@getBlogUpdatePage');
Route::post('blog-update/{id}','AdminBlogController@postBlogUpdatePage');

//Testimonial routes
Route::get('testimonial-manage','TestimonialController@getAddTestimonial');
Route::post('testimonial-manage','TestimonialController@postAddTestimonial');
Route::get('testimonial-update/{id}','TestimonialController@getTestimonialUpdatePage');
Route::post('testimonial-update/{id}','TestimonialController@postTestimonialUpdatePage');

//brand Module
Route::get('brand-manage','BrandController@getBrandManage');
Route::post('brand-manage','BrandController@postBrandManage');
Route::get('brand-update/{token}','BrandController@getBrandUpdatePage');
Route::post('brand-update/{token}','BrandController@postBrandUpdatePage');


//Attributes and attribute values Module
Route::get('attributes','AttributeController@getAttributePage');
Route::post('attributes','AttributeController@postAttributePage');
Route::get('update-attributes/{token}','AttributeController@getUpdateAttributePage');
Route::post('update-attributes/{token}','AttributeController@postUpdateAttributePage');
Route::get('attribute-values/{token}','AttributeController@getAttributeValues');
Route::post('attribute-values/{token}','AttributeController@postAttributeValues');
Route::get('update-attribute-values/{token}/{attrid_token}','AttributeController@getUpdateAttributeValuePage');
Route::post('update-attribute-values/{token}/{attrid_token}','AttributeController@postUpdateAttributeValuePage');


//product combinations routes
Route::get('product-combinations/{token}','CombinationController@getCombinationPage');

//CMS routes
Route::get('cms-manage','CmsController@getAddCms');
Route::post('cms-manage','CmsController@postAddCms');
Route::get('cms-update/{id}','CmsController@getCmsUpdatePage');
Route::post('cms-update/{id}','CmsController@postCmsUpdatePage');

//shipping routes
Route::get('shipping-rule','ShippingController@getShippingRule');
Route::post('shipping-rule','ShippingController@postShippingRule');

// settings admin routes
Route::get('settings','SettingsController@getSettingsPage');
Route::post('settings','SettingsController@postSettingsPage');

//orders routes
Route::get('orders','AdminOrdersController@getOrdersPage');
Route::get('cancellation-orders','AdminOrdersController@getCancellationOrdersPage');
Route::get('approve-cancel-orders/{order_id}','AdminOrdersController@getApproveCancelOrdersPage');
Route::post('reject-request','AdminOrdersController@postRejectOrdersPage');
Route::get('change-order-status/{order_id}/{sel_val}','AdminOrdersController@getChangeOrderStatus');
Route::get('manage-order/{token}','AdminOrdersController@getManageOrder');
Route::post('order-status-update','AdminOrdersController@postOrderStatusUpdate');

Route::post('export-products','ProductController@ExportProducts');


});
