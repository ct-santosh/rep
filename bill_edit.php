<?php
    error_reporting( E_ALL & ~E_WARNING );?>

<html>
    <head>
        <script src="vue.js" ></script>
        <script src="axios.min.js" ></script>
        <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css" />
        <script src="bootstrap/js/bootstrap.min.js" ></script>
    </head>
    <body>

        <div id="app" >
            
            <table>
                <tr>
                    <th>Create Bill</th>
                </tr>
                <tr>
                    <td>
                        <table border="1" class="table table-bordered table-striped">
                            <tr>
                                <th>date</th>
                                <th>customer_name</th>
                                <th>amount</th>
                                <th>tax</th>
                                <th>net</th>
                                <th></th>
                            </tr>
                            <tr>
                                <td><input type="date" v-model="bill['date']" ></td>
                                <td><input type="text" v-model="bill['c_name']" ></td>
                                <td>{{ bill['amount'] }}</td>
                                <td>{{ bill['tax'] }}</td>
                                <td>{{ bill['net'] }}</td>
                                <td><a v-bind:href="'bill_edit.php?bill_id='+bill['id']" class="btn btn-default btn-sm" >VIEW</a></td>
                                <td><input type="button" class="btn btn-danger btn-sm" >X</a></td>
                            </tr>
                        </table>

                        <table border="1" class="table table-bordered table-striped">
                            <tr>
                                <th>sno</th>
                                <th>Product</th>
                                <th>quantity</th>
                                <th>Price</th>
                                <th>Total</th>
                                <th><input type="button" class="btn btn-danger btn-sm" value="+" v-on:click="add_item" ></th>
                            </tr>
                            <tr v-for="v,i in bill_items" >
                                <td>{{ (i+1) }}</td>
                                <td><select v-model="v['product_name']" v-on:change="product_selected(i)" >
                                    <option value="" >Select Product</option>
                                    <option v-for="pd in products" v-bind:value="pd['name']" >{{ pd['name'] }}</option>
                                </select></td>
                                <td><input type="number" v-model="v['quantity']" v-on:change="calculate(i)" ></td>
                                <td>{{ v['price'] }}</td>
                                <td>{{ v['total'] }}</td>
                                <td><input type="button" class="btn btn-danger btn-sm" value="X" v-on:click="delete_item(i)" ></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <div><input type="button" class="btn btn-primary btn-sm" value="Save" v-on:click="save_bills" ></div>

            <div><br><a href="bills.php" class="btn btn-primary btn-sm" >View Bills</a></div>
        </div>
        <script>
            var app = Vue.createApp({
                //el: "#app",
                data: function(){
                    return {
                        "bill": {
                            "id": 0,
                            "date": "",
                            "c_name": "",
                            "amount": 0,
                            "tax": 0,
                            "net": 0
                        },
                        "bill_items": [{
                            "id": 0,
                            "product_name": "",
                            "quantity": 0,
                            "price": 0,
                            "total": 0,
                        }],
                        products: [],
                        edit_mode: <?=$_GET['bill_id']?"true":"false"?>,
                        bill_id: <?=$_GET['bill_id']?$_GET['bill_id']:0 ?>,
           
                    };
                },
                mounted: function(){
                    this.load_products();
                    if( this.edit_mode ){
                        this.load_bill();
                    }
                },
                created: function(){

                },
                methods: {
                    load_products: function(){
                        axios.get("product.php?action=load_products").then(response=>{
                            console.log( response.status );
                            console.log( response.data );
                            this.products = response.data['products'];
                        });
                    },
                    product_selected: function(vi){
                        var product_name = this.bill_items[ vi ]['product_name'];
                        for(var i=0;i<this.products.length;i++){
                            if( this.products[i]['name'] == product_name ){
                                this.bill_items[ vi ]['price'] = this.products[i]['price'];
                            }
                        }
                        this.calculate(vi);
                    },
                    calculate: function(vi){
                        this.bill_items[ vi ]['total'] = Number(this.bill_items[vi]['price'])*Number(this.bill_items[vi]['quantity']);
                        this.calculate_amount();
                    },
                    calculate_amount: function(){
                        var tot = 0;
                        for(var i=0;i<this.bill_items.length;i++){
                            tot = tot + Number(this.bill_items[i]['total']);
                        }
                        this.bill['amount'] = tot;
                        this.bill['tax'] = (tot*.18).toFixed(2);
                        this.bill['net'] = (Number(this.bill['amount']) + Number(this.bill['tax'])).toFixed(2);
                    },
                    add_item: function(){
                        this.bill_items.push({
                            "id": 0,
                            "product_name": "",
                            "quantity": 0,
                            "price": 0,
                            "total": 0,
                        });
                    },
                    delete_item: function(vi){
                        this.bill_items.splice(vi,1);
                    },
                    save_bills: function(){
                        console.log("hi");
                     
                        axios.post("billapi.php", {
                            "action": "save_bill",
                            "bill": JSON.stringify(this.bill),
                            "bill_items": JSON.stringify(this.bill_items),
                        },{
                            'headers': {
                                "Content-Type": "application/x-www-form-urlencoded"
                            }
                        }).then(response=>{
                            alert("Bills Added");  
                            console.log(this.bill);
                            console.log(this.bill_items);
                            this.bill= {
                            "id": 0,
                            "date": "",
                            "c_name": "",
                            "amount": 0,
                            "tax": 0,
                            "net": 0
                        };
                        this.bill_items= [{
                            "id": 0,
                            "product_name": "",
                            "quantity": 0,
                            "price": 0,
                            "total": 0,
                        }]   
                        })
                    },
                    load_bill: function(){
                        // promises 
                        console.log("hi");
                        axios.get("billapi.php?action=read_bill&id=<?=$_GET['bill_id']?>").then(response=>{
                            console.log( response.status );
                            console.log( response.bill );
                            this.bill = response.data['bill'];
                            this.bill_items = response.data['bill_items'];
                        });
                    },
                  
                    
                }
            }).mount("#app");
        </script>
    </body>
</html>