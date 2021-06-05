<template>
    <div>
        <heading class="mb-6">{{this.$route.meta.label}}</heading>
        <div class="flex p-6 justify-between w-1/3">
            <input type="text" v-model="filter.catergory_name" placeholder="Filter" class="form-control form-input form-input-bordered filter-category-input">
            <button type="button" class="btn btn-default text-white bg-success" @click="getCategories()">Filter</button>
            <button class="btn text-white btn-default bg-70" @click="clearFilter">Clear filter</button>
        </div>
        <card class="flex flex-col p-6 justify-center" style="min-height: 300px">
            <table class="table table-striped table-bordered" width="100%">
                <thead>
                    <tr>
                        <th>Category Name</th>
                        <th>Business Count</th>
                    </tr>
                </thead>
                <tbody v-if="categories.length">
                    <tr v-for="category in categories">
                        <td>{{category.name}}</td>
                        <td align="center">{{category.businessCount}}</td>
                    </tr>
                </tbody>
            </table>
        </card>
    </div>
</template>

<script>
export default {
    props: {

    },
    data(){
        return {
            categories: [],
            filter:{
                catergory_name:''
            }
        }
    },
    methods: {
        getCategories(){
            self = this;
            axios
                .get('/api/v1/categories/business-stats', {
                    params: {
                        search: self.filter.catergory_name
                    }
                })
                .then(response => {
                    this.categories = response.data.data;
                    console.log(response.data.data);
                });
        },
        // filterCats(){
        //
        // },
        clearFilter(){
            this.filter.catergory_name = '';
            this.getCategories();
        }
    },
    mounted() {
        this.getCategories();
    },
}
</script>

<style>
.filter-category-input{max-width: 300px;width:50%;}
</style>
