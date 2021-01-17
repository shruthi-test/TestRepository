@include('userfiles.header')

	<div class="container-fluid nopad">
			<div class="banner">
				<div id="slider" class="nivoSlider">
					@foreach($sliders as $key=>$slider)
					<img src="{{ url('/') }}/websiteimages/{{$slider->image}}" title="#{{ $slider->title }}" alt="slider" class="img-responsive">
					<!-- <img src="{{url('/')}}/user_assets/images/newslider1.jpg" title="#caption" alt="slider" class="img-responsive"> -->
					@endforeach
				</div>
			</div>
	</div>
   <div class="container nopad">
	   <div class="row">
		<div class="col-md-3 hidden-md hidden-lg">
			<h4 class="mobile_search side_heading">Refine your Search<span class="faq-t">+</span></h4>
			<div class="mobile_refine" style="display: none;">
				@include('userfiles.sidebar')
			</div>
		</div>
		<div class="col-md-3 hidden-xs hidden-sm">
			@include('userfiles.sidebar')
		</div>
		<div class="col-md-9">
			<div class="product_div">
				<div class="prod_head">
					<h3>Top Products</h3>
				</div>
				<tabs>
					<tab class="nopad">
						<h3 class="heading">Latest</h3>
						<div class="prd_cnt">
							<div class="row">

							@if(sizeof($latest_products)>0)
							@foreach($latest_products as $latest)

								@if($latest->combinations == "Yes")
									@if($latest->allproductsproductattributes)

										<div class="col-md-4 col-sm-4">
													<div class="prod_dtl">
														<div class="prd_img">
															<img src="{{url('/')}}/product_combination_photos/{{$latest->allproductsproductattributes->image}}" class="img-responsive">
															<div class="prod_ovrly">
																<div class="buttons">
																	<!-- <a href="#"><img src="{{url('/')}}/user_assets/images/cart.jpg"></a> -->
																	<a href="{{ url('product') }}/{{ $latest->slug }}"><img src="{{url('/')}}/user_assets/images/view.jpg"></a>
																</div>
															</div>
														</div>
														<div class="prd-btm">
															<h4>{{$latest->product_name}} </h4>

															<form class="addtocart" method="post">
															<input type="hidden" name="selprodid" value="{{$latest->id}}" required />
															<div class="prd-opt">
																<div class="sel-lt">
																	<select name="select_product" class="select_product" proid="{{$latest->id}}" id="sel_pro_{{$latest->id}}" required >
																		@foreach($latest->productvariants as $provar)
																		@if($latest->allproductsproductattributes->id == $provar->id)
																		<option value="{{$provar->id}}" selected>{{$provar->variantname}}</option>
																		@else
																		<option value="{{$provar->id}}" selected>{{$provar->variantname}}</option>
																		@endif
																		@endforeach
																	</select>
																</div>
																<div class="sel-rt">
															  		<label>Qty:</label>
																	<select name="select_qty" class="select_qty" proid="{{$latest->id}}" id="qty_{{$latest->id}}" required>
																		
																		<?php for($i=1;$i<=$latest->allproductsproductattributes->variant_max_qty; $i++) { ?>
									                                        <option value="<?php echo $i;?>"><?php echo $i;?></option>
									                                    <?php } ?>
																			
																	</select>
															  	</div>
															</div>
															<div class="clearfix"></div>
															<div class="prd-crt">
																<div class="sel-lt">
																	<p class="prc" id="sel_price_{{$latest->id}}">
																		
																		@if($latest->percent > 0)
																			<del>&#8377; {{ number_format($latest->allproductsproductattributes->variant_price_tax_excl) }} </del>&#8377; {{ number_format('App\Product'::getCalculateOffer($latest->allproductsproductattributes->variant_price_tax_excl,$latest->percent)) }}
																		@else
																			&#8377; {{ number_format($latest->allproductsproductattributes->variant_price_tax_excl) }}
																		@endif

																	</p>
																</div>
																<div class="sel-rt">
																	<button type="submit" class="ad-crt">Add to Cart</button>
																</div>
															</div>
															</form>

															<div class="clearfix"></div>
														</div>
													</div>
												</div>

									@else

										@if(sizeof($latest->productvariants)>0)
										<div class="col-md-4 col-sm-4">
											<div class="prod_dtl">
												<div class="prd_img">
													<img src="{{url('/')}}/resize_product_photos/{{$latest->image}}" class="img-responsive">
													<div class="prod_ovrly">
														<div class="buttons">
															<!-- <a href="#"><img src="{{url('/')}}/user_assets/images/cart.jpg"></a> -->
															<a href="{{ url('product') }}/{{ $latest->slug }}"><img src="{{url('/')}}/user_assets/images/view.jpg"></a>
														</div>
													</div>
												</div>
												<div class="prd-btm">
													<h4>{{$latest->product_name}}</h4>

													<form class="addtocart" method="post">
													<input type="hidden" name="selprodid" value="{{$latest->id}}" required />
													<div class="prd-opt">
														<div class="sel-lt">
															<select name="select_product" class="select_product" proid="{{$latest->id}}" id="sel_pro_{{$latest->id}}" required >
																@foreach($latest->productvariants as $provar)
																<option value="{{$provar->id}}">{{$provar->variantname}}</option>
																@endforeach
															</select>
														</div>
														<div class="sel-rt">
													  		<label>Qty:</label>
															<select name="select_qty" class="select_qty" proid="{{$latest->id}}" id="qty_{{$latest->id}}" required>
																@foreach($latest->productvariants as $keyyy=>$provar)
																	@if($keyyy == 0)
																		<?php for($i=1;$i<=$provar->variant_max_qty; $i++) { ?>
									                                        <option value="<?php echo $i;?>"><?php echo $i;?></option>
									                                    <?php } ?>
								                                    @endif
																	@break
																@endforeach
															</select>
													  	</div>
													</div>
													<div class="clearfix"></div>
													<div class="prd-crt">
														<div class="sel-lt">
															<p class="prc" id="sel_price_{{$latest->id}}">
																@foreach($latest->productvariants as $keyyy=>$provar)
																	@if($keyyy == 0)
																		@if($latest->percent > 0)
																			<del>&#8377; {{ number_format($provar->variant_price_tax_excl) }} </del>&#8377; {{ number_format('App\Product'::getCalculateOffer($provar->variant_price_tax_excl,$latest->percent)) }}
																		@else
																			&#8377; {{ number_format($provar->variant_price_tax_excl) }}
																		@endif
																		@break
																	@endif
																@endforeach
															</p>
														</div>
														<div class="sel-rt">
															<button type="submit" class="ad-crt">Add to Cart</button>
														</div>
													</div>
													</form>

													<div class="clearfix"></div>
												</div>
											</div>
										</div>
										@endif

									@endif

								@else

									<div class="col-md-4 col-sm-4">
										<div class="prod_dtl">
											<div class="prd_img">
												<img src="{{url('/')}}/resize_product_photos/{{$latest->image}}" class="img-responsive">
												<div class="prod_ovrly">
													<div class="buttons">
														<!-- <a href="#"><img src="{{url('/')}}/user_assets/images/cart.jpg"></a> -->
														<a href="{{ url('product') }}/{{ $latest->slug }}"><img src="{{url('/')}}/user_assets/images/view.jpg"></a>
													</div>
												</div>
											</div>
											<div class="prd-btm">
												<h4>{{$latest->product_name}}</h4>

												<form class="addtocart" method="post">
												<input type="hidden" name="selprodid" value="{{$latest->id}}" required />
												<div class="prd-opt">
													<div class="sel-lt">
														<input type="hidden" name="select_product" value="0" required />
													</div>
													<div class="sel-rt">
												  		<label>Qty:</label>
														<select name="select_qty" class="select_qty" proid="{{$latest->id}}" id="qty_{{$latest->id}}" required>
															<?php for($i=1;$i<=$latest->max_quantity; $i++) { ?>
						                                        <option value="<?php echo $i;?>"><?php echo $i;?></option>
						                                    <?php } ?>
														</select>
												  	</div>
												</div>
												<div class="clearfix"></div>
												<div class="prd-crt">
													<div class="sel-lt">
														<p class="prc" id="sel_price_{{$latest->id}}">
															@if($latest->percent > 0)
																<del>&#8377; {{ number_format($latest->tax_excl_price) }} </del>&#8377; {{ number_format('App\Product'::getCalculateOffer($latest->tax_excl_price,$latest->percent)) }}
															@else
																&#8377; {{ number_format($latest->tax_excl_price) }}
															@endif
														</p>
													</div>
													<div class="sel-rt">
														<button type="submit" class="ad-crt">Add to Cart</button>
													</div>
												</div>
												</form>

												<div class="clearfix"></div>
											</div>
										</div>
									</div>

								@endif


								@if($key>0)

							        @if($key == 2)
							            <div class="clearfix"></div>
							        @endif

						        @endif

							@endforeach

							@else

							<h5>- No Latest Products Found -</h5>

							@endif
							
							</div>
						</div>
						
					</tab>
					<tab>
						<h3 class="heading">Bestseller</h3>
						<div class="row">
							
							@if(sizeof($bestsellers)>0)
							@foreach($bestsellers as $latest)

								@if($latest->combinations == "Yes")

									@if($latest->allproductsproductattributes)

										<div class="col-md-4 col-sm-4">
													<div class="prod_dtl">
														<div class="prd_img">
															<img src="{{url('/')}}/product_combination_photos/{{$latest->allproductsproductattributes->image}}" class="img-responsive">
															<div class="prod_ovrly">
																<div class="buttons">
																	<!-- <a href="#"><img src="{{url('/')}}/user_assets/images/cart.jpg"></a> -->
																	<a href="{{ url('product') }}/{{ $latest->slug }}"><img src="{{url('/')}}/user_assets/images/view.jpg"></a>
																</div>
															</div>
														</div>
														<div class="prd-btm">
															<h4>{{$latest->product_name}} </h4>

															<form class="addtocart" method="post">
															<input type="hidden" name="selprodid" value="{{$latest->id}}" required />
															<div class="prd-opt">
																<div class="sel-lt">
																	<select name="select_product" class="best_select_product" proid="{{$latest->id}}" id="best_sel_pro_{{$latest->id}}" required >
																		@foreach($latest->productvariants as $provar)
																		@if($latest->allproductsproductattributes->id == $provar->id)
																		<option value="{{$provar->id}}" selected>{{$provar->variantname}}</option>
																		@else
																		<option value="{{$provar->id}}" selected>{{$provar->variantname}}</option>
																		@endif
																		@endforeach
																	</select>
																</div>
																<div class="sel-rt">
															  		<label>Qty:</label>
																	<select name="select_qty" class="best_select_qty" proid="{{$latest->id}}" id="best_qty_{{$latest->id}}" required>
																		
																		<?php for($i=1;$i<=$latest->allproductsproductattributes->variant_max_qty; $i++) { ?>
									                                        <option value="<?php echo $i;?>"><?php echo $i;?></option>
									                                    <?php } ?>
																			
																	</select>
															  	</div>
															</div>
															<div class="clearfix"></div>
															<div class="prd-crt">
																<div class="sel-lt">
																	<p class="prc" id="best_sel_price_{{$latest->id}}">
																		
																		@if($latest->percent > 0)
																			<del>&#8377; {{ number_format($latest->allproductsproductattributes->variant_price_tax_excl) }} </del>&#8377; {{ number_format('App\Product'::getCalculateOffer($latest->allproductsproductattributes->variant_price_tax_excl,$latest->percent)) }}
																		@else
																			&#8377; {{ number_format($latest->allproductsproductattributes->variant_price_tax_excl) }}
																		@endif

																	</p>
																</div>
																<div class="sel-rt">
																	<button type="submit" class="ad-crt">Add to Cart</button>
																</div>
															</div>
															</form>

															<div class="clearfix"></div>
														</div>
													</div>
												</div>

									@else

										@if(sizeof($latest->productvariants)>0)

											<div class="col-md-4 col-sm-4">
												<div class="prod_dtl">
													<div class="prd_img">
														<img src="{{url('/')}}/resize_product_photos/{{$latest->image}}" class="img-responsive">
														<div class="prod_ovrly">
															<div class="buttons">
																<!-- <a href="#"><img src="{{url('/')}}/user_assets/images/cart.jpg"></a> -->
																<a href="{{ url('product') }}/{{ $latest->slug }}"><img src="{{url('/')}}/user_assets/images/view.jpg"></a>
															</div>
														</div>
													</div>
													<div class="prd-btm">
														<h4>{{$latest->product_name}}</h4>

														<form class="addtocart" method="post">
														<input type="hidden" name="selprodid" value="{{$latest->id}}" required />
														<div class="prd-opt">
															<div class="sel-lt">
																<select name="select_product" class="best_select_product" proid="{{$latest->id}}" id="best_sel_pro_{{$latest->id}}" required >
																	@foreach($latest->productvariants as $provar)
																	<option value="{{$provar->id}}">{{$provar->variantname}}</option>
																	@endforeach
																</select>
															</div>
															<div class="sel-rt">
														  		<label>Qty:</label>
																<select name="select_qty" class="best_select_qty" proid="{{$latest->id}}" id="best_qty_{{$latest->id}}" required>
																	@foreach($latest->productvariants as $keyyy=>$provar)
																		@if($keyyy == 0)
																			<?php for($i=1;$i<=$provar->variant_max_qty; $i++) { ?>
										                                        <option value="<?php echo $i;?>"><?php echo $i;?></option>
										                                    <?php } ?>
									                                    @endif
																		@break
																	@endforeach
																</select>
														  	</div>
														</div>
														<div class="clearfix"></div>
														<div class="prd-crt">
															<div class="sel-lt">
																<p class="prc" id="best_sel_price_{{$latest->id}}">
																	@foreach($latest->productvariants as $keyyy=>$provar)
																		@if($keyyy == 0)
																			@if($latest->percent > 0)
																				<del>&#8377; {{ number_format($provar->variant_price_tax_excl) }} </del>&#8377; {{ number_format('App\Product'::getCalculateOffer($provar->variant_price_tax_excl,$latest->percent)) }}
																			@else
																				&#8377; {{ number_format($provar->variant_price_tax_excl) }}
																			@endif
																			@break
																		@endif
																	@endforeach
																</p>
															</div>
															<div class="sel-rt">
																<button type="submit" class="ad-crt">Add to Cart</button>
															</div>
														</div>
														</form>

														<div class="clearfix"></div>
													</div>
												</div>
											</div>
										@endif

									@endif

								@else

									<div class="col-md-4 col-sm-4">
										<div class="prod_dtl">
											<div class="prd_img">
												<img src="{{url('/')}}/resize_product_photos/{{$latest->image}}" class="img-responsive">
												<div class="prod_ovrly">
													<div class="buttons">
														<!-- <a href="#"><img src="{{url('/')}}/user_assets/images/cart.jpg"></a> -->
														<a href="{{ url('product') }}/{{ $latest->slug }}"><img src="{{url('/')}}/user_assets/images/view.jpg"></a>
													</div>
												</div>
											</div>
											<div class="prd-btm">
												<h4>{{$latest->product_name}}</h4>

												<form class="addtocart" method="post">
												<input type="hidden" name="selprodid" value="{{$latest->id}}" required />
												<div class="prd-opt">
													<div class="sel-lt">
														<input type="hidden" name="select_product" value="0" required />
													</div>
													<div class="sel-rt">
												  		<label>Qty:</label>
														<select name="select_qty" class="best_select_qty" proid="{{$latest->id}}" id="best_qty_{{$latest->id}}" required>
															<?php for($i=1;$i<=$latest->max_quantity; $i++) { ?>
						                                        <option value="<?php echo $i;?>"><?php echo $i;?></option>
						                                    <?php } ?>
														</select>
												  	</div>
												</div>
												<div class="clearfix"></div>
												<div class="prd-crt">
													<div class="sel-lt">
														<p class="prc" id="best_sel_price_{{$latest->id}}">
															@if($latest->percent > 0)
																<del>&#8377; {{ number_format($latest->tax_excl_price) }} </del>&#8377; {{ number_format('App\Product'::getCalculateOffer($latest->tax_excl_price,$latest->percent)) }}
															@else
																&#8377; {{ number_format($latest->tax_excl_price) }}
															@endif
														</p>
													</div>
													<div class="sel-rt">
														<button type="submit" class="ad-crt">Add to Cart</button>
													</div>
												</div>
												</form>

												<div class="clearfix"></div>
											</div>
										</div>
									</div>

								@endif


								@if($key>0)

							        @if($key == 2)
							            <div class="clearfix"></div>
							        @endif

						        @endif

							@endforeach

							@else

								<h5>- No Best Selling Products Found -</h5>

							@endif

						</div>
					</tab>
				</tabs>
			<!-- </div>
			<div class="event_div">
				<div class="event_head">
					<h3>Events</h3>
				</div>
				<div class="head_bg"></div>
				<div class="owl-carousel events owl-theme">
					<div>
						<img src="images/event1.jpg" class="img-responsive">
					</div>
					<div>
						<img src="images/event1.jpg" class="img-responsive">
					</div>
				</div>
			</div> -->
			<div class="blog_div">
				<div class="event_head">
					<h3>Latest Blog</h3>
				</div>
				<div class="head_bg"></div>
				<div class="owl-carousel blog owl-theme">

					@if(sizeof($blogs)>0)
						@foreach($blogs as $blog)
						
						<div>
							<a href="{{ url('blog-detail') }}/{{ $blog->slug }}">
							@if($blog->image)
							<img src="{{url('/')}}/blog_photos/{{$blog->image}}" class="img-responsive">
							@else
							<img src="{{url('/')}}/default_images/blog_default.png" class="img-responsive">
							@endif
							<span class="blog_date">{{date('d',strtotime($blog->created_at))}} <br>{{date('M',strtotime($blog->created_at))}}<br>{{date('Y',strtotime($blog->created_at))}}</span>
							<!-- <div class="blog_cnt"><?php echo substr($blog->description, 0, 80); ?>... </div> -->
							<h4 class="blog_cnt">{{ $blog->title }}</h4>
							</a>
						</div>
						
						@endforeach
					@else
						<h5>- No Blogs Found -</h5>
					@endif

				</div>
			</div>

			<!-- ads block -->
			<div class="ad-blk">
						<img src="{{url('/')}}/user_assets/images/ads.jpg" class="img-responsive">
			</div>
		</div>
		</div>
	</div>
</div>

	<!-- mostview products -->

	<div class="mst-prd">
		<div class="container nopad">
				<h2>Most View Products</h2>
				<span></span>
				<div class="owl-carousel mst-slide owl-theme">
					 @if(sizeof($most_viewed_products)>0)

                		@foreach($most_viewed_products as $relate)
						<div>
							<a href="{{ url('product') }}/{{ $relate['slug'] }}">
								<img src="{{ $relate['image_url'] }}/{{ $relate['image'] }}" class="img-responsive">
								<div class="mst-dtl">
									<p><!--{{ $relate['product_code'] }}--> @if($relate['var_product_name']) {{ $relate['var_product_name'] }} @else {{ $relate['product_name'] }} @endif</p>  
									
									<div class="rate_div" style="margin-left:5px;">
		                                <ul class="list-inline">
		                                  <div class='starrr3'>
		                                    <script>
		                                    $('.starrr3').starrr({
		                                    rating: "{{ $relate['avg_rate'] == null ? 0 : $relate['avg_rate'] }}",
		                                    readOnly: true
		                                    });
		                                    </script>
		                                  </div>
		                                </ul>
		                            </div>

									@if(count($relate['attributes']) > 0)
		                                @if($relate['percent'] > 0)
		                                	<p class="mst-price"><del>₹ {{ number_format($relate['tax_excl_price']) }}</del> ₹ {{ number_format('App\Product'::getCalculateOffer($relate['tax_excl_price'],$relate['percent'])) }} </p>
		                                  </p>
		                                @else
		                                	<p class="mst-price">₹ {{ number_format($relate['tax_excl_price']) }} </p>
		                                @endif
		                            @else
		                                @if($relate['percent'] > 0) 
		                                	<p class="mst-price"><del>₹ {{ number_format($relate['tax_excl_price']) }}</del> ₹ {{ number_format('App\Product'::getCalculateOffer($relate['tax_excl_price'],$relate['percent'])) }} </p>
		                                @else 
		                                	<p class="mst-price">₹ {{ number_format($relate['tax_excl_price']) }} </p>
		                                @endif
		                            @endif
								</div>
							</a>
						</div>
						@endforeach

	                @else
	                  <p>No Related Prodcuts found</p>
	                @endif
						
				</div>
		</div>
	</div>

	@include('userfiles.footer')

	<script src="{{url('user_assets/js/sweetalert.min.js')}}"></script>

	<script type="text/javascript">

		$('.select_product').change(function() {

			var proid = $(this).attr('proid');
			var provarid = $(this).val();
			var provarqty = $('#qty_'+proid).val();

			$.ajax({
		        type : 'post',
		        url : "{{url('')}}/change-product-variant",
		        data : { 'proid':proid,'provarid':provarid,'provarqty':provarqty},
		        headers: {'X-CSRF-Token': csrf_token},

		        success: function (response) {

		        	var prod_div = '';

		            if(response.result == 1) {

		                if(response.offer_prnct > 0) {
						prod_div += '<del>&#8377; '+response.price+' </del>&#8377; '+response.offer_price;
		                } else {
						prod_div += '&#8377; '+response.price;
		                }

		                $('#sel_price_'+proid).html(prod_div);

		            } else {

		            	swal(response.error_msg);
		            	$('#sel_pro_'+provarid).val(provarid);
		            	$('#qty_'+provarid).val(1);

		            }

		        }

		    });

		});


		$('.select_qty').change(function() {

			var proid = $(this).attr('proid');
			var provarid = $('#sel_pro_'+proid).val();
			if(provarid) {
				provarid = provarid;
			} else {
				provarid = 0;
			}
			var provarqty = $(this).val();

			$.ajax({
		        type : 'post',
		        url : "{{url('')}}/change-product-quantity",
		        data : { 'proid':proid,'provarid':provarid,'provarqty':provarqty},
		        headers: {'X-CSRF-Token': csrf_token},

		        success: function (response) {

		        	var prod_div = '';

		            if(response.result == 1) {

		                if(response.offer_prnct > 0) {
						prod_div += '<del>&#8377; '+response.price+' </del>&#8377; '+response.offer_price;
		                } else {
						prod_div += '&#8377; '+response.price;
		                }

		                $('#sel_price_'+proid).html(prod_div);

		            } else {

		            	swal(response.error_msg);
		            	$('#sel_pro_'+provarid).val(provarid);
		            	$('#qty_'+provarid).val(1);

		            }

		        }

		    });

		});


		$('.best_select_product').change(function() {

			var proid = $(this).attr('proid');
			var provarid = $(this).val();
			var provarqty = $('#best_qty_'+proid).val();

			$.ajax({
		        type : 'post',
		        url : "{{url('')}}/change-product-variant",
		        data : { 'proid':proid,'provarid':provarid,'provarqty':provarqty},
		        headers: {'X-CSRF-Token': csrf_token},

		        success: function (response) {

		        	var prod_div = '';

		            if(response.result == 1) {

		                if(response.offer_prnct > 0) {
						prod_div += '<del>&#8377; '+response.price+' </del>&#8377; '+response.offer_price;
		                } else {
						prod_div += '&#8377; '+response.price;
		                }

		                $('#best_sel_price_'+proid).html(prod_div);

		            } else {

		            	swal(response.error_msg);
		            	$('#best_sel_pro_'+provarid).val(provarid);
		            	$('#best_qty_'+provarid).val(1);

		            }

		        }

		    });

		});


		$('.best_select_qty').change(function() {

			var proid = $(this).attr('proid');
			var provarid = $('#best_sel_pro_'+proid).val();
			if(provarid) {
				provarid = provarid;
			} else {
				provarid = 0;
			}
			var provarqty = $(this).val();

			$.ajax({
		        type : 'post',
		        url : "{{url('')}}/change-product-quantity",
		        data : { 'proid':proid,'provarid':provarid,'provarqty':provarqty},
		        headers: {'X-CSRF-Token': csrf_token},

		        success: function (response) {

		        	var prod_div = '';

		            if(response.result == 1) {

		                if(response.offer_prnct > 0) {
						prod_div += '<del>&#8377; '+response.price+' </del>&#8377; '+response.offer_price;
		                } else {
						prod_div += '&#8377; '+response.price;
		                }

		                $('#best_sel_price_'+proid).html(prod_div);

		            } else {

		            	swal(response.error_msg);
		            	$('#best_sel_pro_'+provarid).val(provarid);
		            	$('#best_qty_'+provarid).val(1);

		            }

		        }

		    });

		});
		

		function add_to_cart(product_id, e) {
		    e.preventDefault();

		    var url = "{{ url('add-to-cart') }}";
		    var qtyVal = $('#quanity_sel').val();
		    var feature_status = "{{ $feature_status }}";
		    var feature_id = -1;

		    if(feature_status == 1) {

		      if(items.length == 0) {

		        swal({
		            html:true,
		            title: "",
		            text: 'Select Product Features.',
		            icon: "error"
		          });

		        $('.combinationselbox').css( "border", "1px solid red" );
		        setTimeout(function()
		        {
		           $('.combinationselbox').css( "border", "" );
		        },3000);
		        return false;
		        // alert('Select Weight.');
		        // return false;

		      }

		    } else {
		      feature_id = -1;
		    }

		    if(qtyVal<=0) {
		      swal({
		        html:true,
		        title: "",
		        text: 'Quantity should be greater than 0',
		        icon: "error"
		      });
		      return false;
		      // alert('Quantity should be greater than 0');
		      // return false;
		    }

		    var postData =  {
		    "_token": "{{ csrf_token() }}",
		    "id": product_id,
		    'feature_data' : items,
		    'qty_val'  : qtyVal,
		    'feature_status': feature_status,
		    };

		    $.ajax({
		        type: "post",
		        url: url,
		        data: postData,
		        contentType: "application/x-www-form-urlencoded",
		        success: function(data) {

		           var parsedata = jQuery.parseJSON( data );
		           console.log(parsedata);

		           if(parsedata.status == 200) {

		              swal({title: "", text: parsedata.info, icon: "success"})
		                .then((value) => {
		                  location.reload();
		              });

		           } else {

		              // alert(parsedata.info);
		              swal({
		                html:true,
		                title: "",
		                text: parsedata.info,
		                icon: "error"
		              });
		              $('.combinationselbox').css( "border", "1px solid red" );
		              setTimeout(function() {
		                $('.combinationselbox').css( "border", "" );
		              },5000);

		              return false;
		              // return false;
		           }
		        },
		        error: function(jqXHR, textStatus, errorThrown) {
		            console.log(errorThrown);
		        }
		    });
		};


		$('.addtocart').submit(function(event){
			event.preventDefault();

			var frmdata = new FormData($(this)[0]);
			var url = "{{ url('list-product-add-to-cart') }}";

			$.ajax({
		        type: "post",
		        url: url,
		        data: frmdata,
		        processData: false,
        		contentType: false,
        		headers: {'X-CSRF-Token': csrf_token},
		        success: function(data) {

		           var parsedata = jQuery.parseJSON( data );
		           console.log(parsedata);

		           if(parsedata.status == 200) {

		              swal({title: "", text: parsedata.info, icon: "success"})
		                .then((value) => {
		                  location.reload();
		              });

		           } else {

		              // alert(parsedata.info);
		              swal({
		                html:true,
		                title: "",
		                text: parsedata.info,
		                icon: "error"
		              });
		              $('.combinationselbox').css( "border", "1px solid red" );
		              setTimeout(function() {
		                $('.combinationselbox').css( "border", "" );
		              },5000);

		              return false;
		              // return false;
		           }
		        },
		        error: function(jqXHR, textStatus, errorThrown) {
		            console.log(errorThrown);
		        }
		    });

		});


	</script>