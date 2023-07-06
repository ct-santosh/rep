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
                    <td><div><br><a href="products.html" class="btn btn-primary btn-sm" >View Products</a></div></td>
                </tr>
                <tr>
                    <th>BILLS</th>      
                </tr>
                <tr>
                    <td>
                        <div><br><a href="bill_edit.php" class="btn btn-primary btn-sm" >Add Bills</a></div>       
                        <!-- <div><a href="bill_edit.php" class="btn btn-primary btn-sm" >Add Bill</a></div> -->
                        <table border="1" class="table table-bordered table-striped">
                            <tr>
                                <th>date</th>
                                <th>customer_name</th>
                                <th>amount</th>
                                <th>tax</th>
                                <th>net</th>
                                <th></th>
                            </tr>
                            <tr v-for="v,i in bills" >
                                <td>{{ v['date'] }}</td>
                                <td>{{ v['c_name'] }}</td>
                                <td>{{ v['amount'] }}</td>
                                <td>{{ v['tax'] }}</td>
                                <td>{{ v['net'] }}</td>
                                <td><a v-bind:href="'bill_edit.php?bill_id='+v['id']" class="btn btn-default btn-sm" >VIEW</a></td>
                                <td><input type="button" class="btn btn-danger btn-sm" value="X" v-on:click="delete_bill(i)"></a></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
        <script>
            var app = Vue.createApp({
                //el: "#app",
                data: function(){
                    return {
                        "message": "Yes ok.",
                        "bills": [],
                        "new_bill": {
                            "id": 0,
                            "date": "",
                            "c_name": "",
                            "amount": 0,
                            "tax": 0,
                            "net": 0
                        }
                    };
                },
                mounted: function(){
                    this.load_bills();
                },
                created: function(){
                },
                methods: {

                    load_bills: function(){
                        axios.get("billapi.php?action=load_bills").then(response=>{
                            console.log( response.status );
                            console.log( response.data );
                            this.bills = response.data['bills'];
                        });
                    },
                    delete_bill: function(vi){
                        var bill_id=this.bills[vi]['id'];
                        axios.get("billapi.php?action=delete_bill&id="+bill_id).then(response=>{
                            console.log(response.status);
                            console.log(response.data);
                            console.log(bill_id);
                            this.load_bills();
                        });
                    }
                }

            }).mount("#app");
        </script>
    </body>
</html>