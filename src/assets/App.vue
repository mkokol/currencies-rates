<template>
    <div id="app">
        <table>
            <thead>
                <tr>
                    <td v-on:click="fetchAll('id')">
                        ID
                    </td>
                    <td v-on:click="fetchAll('currency_from')">
                        Currency From
                    </td>
                    <td v-on:click="fetchAll('currency_to')">
                        Currency To
                    </td>
                    <td v-on:click="fetchAll('rate')">
                        Rate
                    </td>
                    <td>
                        &nbsp;
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr v-for="rate in rates" :key="rate.id">
                    <td>
                        {{ rate.id }}
                    </td>
                    <td>
                        {{ rate.currencyFrom }}
                    </td>
                    <td>
                        {{ rate.currencyTo }}
                    </td>
                    <td>
                        {{ rate.rate }}
                    </td>
                    <td>
                        <button v-on:click="deleteRow(rate.id)">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import axios from 'axios'

export default {
    name: 'app',
    data () {
        return {
            sortBy: 'id',
            sortOrder: 'asc',
            rates: []
        }
    },
    methods: {
        fetchAll (sortBy = 'id') {
            if (sortBy === this.sortBy && this.sortOrder === 'asc') {
                this.sortOrder = 'desc'
            } else {
                this.sortBy = sortBy
                this.sortOrder = 'asc'
            }

            axios
                .get(
                    'http://127.0.0.1:8888/api/v1/currency/rates',
                    {
                        params: {
                            sort_by: this.sortBy,
                            sort_order: this.sortOrder
                        }
                    }
                )
                .then(
                    resp => {
                        this.rates = resp.data.rates
                    }
                )
                .catch(
                    error => {
                        console.log(error)
                    }
                )
        },
        deleteRow (id) {
            axios
                .delete(
                    'http://127.0.0.1:8888/api/v1/currency/rate',
                    {
                        params: {
                            id: id
                        }
                    }
                )
                .then(
                    resp => {
                        this.fetchAll()
                    }
                )
                .catch(
                    error => {
                        console.log(error)
                    }
                )
        }
    },
    mounted () {
        this.fetchAll()
    }
}
</script>

<style lang="scss" scoped>
    #app {
        font-family: 'Avenir', Helvetica, Arial, sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        text-align: center;
        color: #2c3e50;
        margin-top: 10px;
    }

    table {
        margin: auto;
        width: 100%;
        max-width: 960px;
        border: 1px solid #168006;

        thead {
            tr {
                text-align: left;
                background: #2c3e50;
                color: #f9ecec;

                td {
                    cursor: pointer;
                }
            }
        }

        tbody {
            tr {
                text-align: right;
                border-bottom: 1px dotted #168006;
            }
        }

        td {
            padding: 6px;
        }
    }

    button {
        display: inline-block;
        background: #e63c45;
        border: none;
        border-radius: 4px;
        color: #ffffff;
        text-decoration: none;
        padding: 8px 12px;
        font-size: 14px;
        font-weight: 300;
        line-height: 16px;
        cursor: pointer;
        text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.2);
        box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.2), inset 0 1px 0 0 rgba(255, 255, 255, 0.2);

        &:hover {
            color: #ffffff;
            background: #a82c32;
            text-decoration: none;
        }
    }
</style>
