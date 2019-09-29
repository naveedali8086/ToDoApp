<template>


    <div class="row justify-content-center">

        <div class="col-12" v-if="show_success_msg">

            <div class="alert alert-success" role="alert">
                {{message}}
            </div>

        </div>

        {{ /* Add 'To-Do' Section */ }}
        <div class="col-12 col-md-5 mb-5">

            <div class="card">

                <h3 class="card-header">Add To-Do</h3>

                <div class="card-body">

                    <div class="form-group">

                        <label>New To-Do</label>

                        <input type="text" class="form-control" v-model="to_do.desc" @keyup.enter="addToDo()"
                               v-bind:disabled="disable_controls">

                        <small class="form-text text-muted">Hit 'Enter' or press 'Save' button</small>

                    </div>

                    <button type="button" class="btn btn-primary" @click="addToDo()" v-bind:disabled="disable_controls">
                        Save
                    </button>

                </div>

            </div>

        </div>

        {{ /* 'To-Dos' List Section */ }}
        <div class="col-12 col-md-7">

            <div class="card">

                <h3 class="card-header">To-Do List</h3>

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover" v-show="to_do_list.length">

                            <thead>

                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">To-Dos</th>
                                <th scope="col" class="text-center">Actions</th>
                            </tr>

                            </thead>

                            <tbody>

                            <tr v-for="to_do in to_do_list">

                                <th scope="row">{{to_do.id}}</th>

                                <td>

                                    <div v-if="to_do !== editing_to_do">{{to_do.desc}}</div>

                                    <input v-if="to_do === editing_to_do" type="text" class="form-control"
                                           v-model="to_do.desc" @blur="editingToDo(to_do)"/>

                                </td>

                                <td class="text-center">

                                    <a href="#" v-on:click.prevent="editToDo(to_do)" class="btn btn-primary"
                                       role="button"

                                       :class="[to_do === editing_to_do ? 'd-none':'d-inline']">

                                        <i class="fas fa-pen"></i>

                                    </a>

                                    <a href="#" v-on:click.prevent="deleteToDo(to_do)" class="btn btn-danger"
                                       role="button">

                                        <i :class="[ to_do.is_deleting? 'fas fa-spinner fa-spin':'fas fa-trash-alt']"></i>

                                    </a>

                                    <a href="#" v-on:click.prevent="toggleToDoStatus(to_do)" class="btn "
                                       role="button"
                                       :class="{'btn-success' : to_do.is_completed,
                                       'btn-light' : !to_do.is_completed}">

                                        <i :class="[to_do.is_toggling_status? 'fas fa-spinner fa-spin':
                                        (to_do.is_completed ? 'far fa-check-circle':'far fa-circle')]"></i>

                                    </a>

                                </td>

                            </tr>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>
        </div>


    </div>

</template>

<script>
    export default {

        data() {

            return {

                to_do: {

                    id: '',
                    desc: '',
                    is_completed: ''

                },

                disable_controls: false,

                to_do_list: [],

                editing_to_do: {},

                show_success_msg: false,
                message: ''

        }

        },

        created() {
            this.getToDos();
        },

        methods: {

            addToDo() {

                this.disable_controls = true;

                axios.post('create-todo', this.to_do)
                    .then(response => {

                        this.disable_controls = false;

                        if (response.data.has_err) {

                            alert(response.data.err);

                        } else {

                            this.message = 'Added Successfully!';
                            this.show_success_msg = true;
                            this.to_do.desc = '';
                            this.getToDos();

                        }

                    })
                    .catch(error => {

                        this.disable_controls = false;

                        this.showErrors(error);

                    });

            },

            getToDos() {

                axios.get('to-do-list').then(response => {

                    this.to_do_list = response.data;

                }).catch(error => {

                    this.showErrors(error);

                });

            },

            toggleToDoStatus(to_do) {

                var msg = to_do.is_completed ? 'incomplete' : 'complete';

                if (confirm('Do you want to mark this To-Do as ' + msg + '?')) {

                    to_do.is_toggling_status = true;

                    axios.post('toggle-status/' + to_do.id).then(response => {

                        if (response.data.has_err) {

                            to_do.is_toggling_status = false;
                            alert(response.data.err);

                        } else {

                            this.message = to_do.is_completed ? 'Marked incompleted Successfully' : 'Marked completed Successfully';
                            this.show_success_msg = true;
                            this.getToDos();

                        }

                    }).catch(error => {

                        this.showErrors(error);

                    });

                }

            },

            deleteToDo(to_do) {

                if (confirm('Do you want to delete this To-Do?')) {

                    to_do.is_deleting = true;

                    axios.post('delete-todo/' + to_do.id).then(response => {

                        if (response.data.has_err) {

                            to_do.is_deleting = false;
                            alert(response.data.err);

                        } else {

                            this.message = 'Deleted Successfully!';
                            this.show_success_msg = true;
                            this.getToDos();

                        }

                    }).catch(error => {

                        this.showErrors(error);

                    });

                }


            },

            editToDo(to_do) {

                this.editing_to_do = to_do;

            },

            editingToDo(to_do) {

                this.editing_to_do = {};

                axios.post('edit-todo/' + to_do.id, to_do).then(response => {

                    if (response.data.has_err) {

                        alert(response.data.err);

                    } else {

                        this.message = 'Updated Successfully!';
                        this.show_success_msg = true;
                        this.getToDos();

                    }

                }).catch(error => {

                    this.showErrors(error);

                });

            },

            /* Utility Method */
            showErrors(error_obj) {

                console.log(error_obj);

                let response = error_obj.response;

                let errs_list = '';

                /* Validation errors by Laravel */
                if (response.status === 422) {

                    let errors = response.data.errors;

                    for (let attribute in errors) {

                        let attr_err = errors[attribute].join().replace(',', "\n") + "\n";

                        errs_list += attr_err;

                    }

                    alert(errs_list);

                } else {

                    alert(error_obj.message);

                }

            }

        },

    }
</script>