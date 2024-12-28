<x-layouts>

    <div class="container">

        <div class="row" id="storeProduct">

                <form class="row g-3" id="storeProduct">

                    <div class="mb-3">
                        <label for="product_name" class="form-label">Product name</label>
                        <input type="text" id="product_name" placeholder="Product name" required>
                      </div>

                      <div class="mb-3">
                        <label for="product_qty_in_stock" class="form-label">Quantity in stock</label>
                        <input type="number" id="product_qty_in_stock" placeholder="Quantity in stock" required min="0">
                      </div>

                      <div class="mb-3">
                        <label for="product_price_per_item" class="form-label">Price per item</label>
                        <input type="text" id="product_price_per_item" placeholder="Price per item" required min="0">
                      </div>

                      <div class="mb-3">
                        <button type="submit" class="btn btn-sm btn-success">Add Product</button>
                      </div>
                </form>
           
        </div>


        <div class="row" id="updateProduct">

            <form class="row g-3" id="updateProduct">

                <div class="mb-3">
                    <label for="edit_product_name" class="form-label">Product name</label>
                    <input type="text" id="edit_product_name" placeholder="Product name" required>
                    <input type="hidden" id="edit_product_id">
                  </div>

                  <div class="mb-3">
                    <label for="edit_product_qty_in_stock" class="form-label">Quantity in stock</label>
                    <input type="number" id="edit_product_qty_in_stock" placeholder="Quantity in stock" required>
                  </div>

                  <div class="mb-3">
                    <label for="edit_product_price_per_item" class="form-label">Price per item</label>
                    <input type="text" id="edit_product_price_per_item" placeholder="Price per item" required>
                  </div>

                  <div class="mb-3">
                    <button type="submit" class="btn btn-sm btn-info">Update Product</button>
                  </div>
            </form>
       
    </div>
        
    </div>

        <table class="table table-hover table-responsive" id="products">
            <thead>
                <tr>
                  <th scope="col">Product name</th>
                  <th scope="col">Quantity in stock</th>
                  <th scope="col">Price per item</th>
                  <th scope="col">Datetime submitted</th>
                  <th scope="col">Total value number</th>
                </tr>
              </thead>
              <tbody>
                <tr class="product-row"></tr>
              </tbody>
          </table>
          <table class="table" id="total">
              <tbody>
                <tr>
                  <td colspan="3" style="text-align:right" class="fw-bolder">Total Value numbers</td>
                  <td id="totalValueNumber">0.00</td>
                </tr>
              </tbody>
          </table>


      <script>

        //displaying product information on table
         function getProducts() {

            $('#updateProduct').hide();

            $.get('/products', function(data) {

                $('#products tbody').empty();
                
                    let totalValueNumbers = 0.00;

                    data.forEach(product => {

                        let price = 0;

                        price = product.product_qty_in_stock * product.product_price_per_item;
                       
                        totalValueNumbers += price;

                        $('#products tbody').append(`
                            <tr>
                                <td class="product-name">${product.product_name}</td>
                                <td class="product-qty-in-stock">${product.product_qty_in_stock}</td>
                                <td class="product-price-per-item">${product.product_price_per_item}</td>
                                <td>${product.datetime_submitted}</td>
                                <td>${price}</td>
                                <td>
                                    <button type="button" class="btn btn-warning text-white edit-product" data-product-id="${product.id}">Edit</button>
                                </td>
                            </tr>
                        `);
               
                    });

                   $('#totalValueNumber').text(totalValueNumbers);

                    
           });
       }

       //Register new product
       $('#storeProduct').on('submit', function(e) {
            e.preventDefault();

            const product = {
                product_name: $('#product_name').val(),
                product_price_per_item: $('#product_price_per_item').val(),
                product_qty_in_stock: $('#product_qty_in_stock').val(),
            };

            $.ajax({
                url: '/products',
                method: 'POST',
                data: product,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {

                    $('#product_name').val(''),
                    $('#product_price_per_item').val(''),
                    $('#product_qty_in_stock').val(''),
                    getProducts();
                    alert(response.message);
                }
            });
        });


        //Invoke product for edit purposes
        $(document).on('click', '.edit-product', function () {
                const id = $(this).data('product-id');

                const row = $(this).closest('tr');
                const productName = row.find('.product-name').text();
                const productPricePerItem = row.find('.product-price-per-item').text()
                const productQtyInStock = row.find('.product-qty-in-stock').text()

                $('#edit_product_id').val(id);
                $('#edit_product_name').val(productName);
                $('#edit_product_price_per_item').val(productPricePerItem);
                $('#edit_product_qty_in_stock').val(productQtyInStock);

                $('#storeProduct').hide();
                $('#updateProduct').show();

                console.log(productName);
                
        });

        getProducts();


        $('#updateProduct').on('submit', function(e) {

            e.preventDefault();

            const productId = $('#edit_product_id').val(),

             product = {
                product_id: productId,
                product_name: $('#edit_product_name').val(),
                product_price_per_item: $('#edit_product_price_per_item').val(),
                product_qty_in_stock: $('#edit_product_qty_in_stock').val(),
            };

            $.ajax({
                url: '/products/' + productId,
                method: 'PUT',
                data: product,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {

                    $('#edit_product_name').val(''),
                    $('#edit_product_price_per_item').val(''),
                    $('#edit_product_qty_in_stock').val(''),

                    $('#updateProduct').hide();
                    $('#storeProduct').show();

                    alert(response.message);

                    getProducts();

                   
                }
            });
        });

      </script>
</x-layouts>