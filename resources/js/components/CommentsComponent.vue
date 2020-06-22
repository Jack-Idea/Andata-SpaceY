<template>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <button class="btn btn-secondary btn-sm uk-align-right" @click.prevent="visibility = !visibility"><span v-if="visibility">Отменить</span><span v-if="!visibility">Комментировать</span></button>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <p v-if="errors.length" class="error-msg">
                    <b>Пожалуйста исправьте указанные ошибки:</b>
                    <ul>
                      <li v-for="error in errors">{{ error }}</li>
                    </ul>
                </p>
                <form v-if="visibility == true" class="uk-animation-slide-right">
                    <div class="uk-margin d-md-flex">
                        <div class="">
                            <span class="d-block">
                                Имя пользователя:  
                                <span v-if="!username" class="uk-animation-shake" uk-icon="icon: ban; ratio: 0.7;" style="color: red;"></span>
                                <span v-if="username" class="uk-animation-shake" uk-icon="icon: check; ratio: 0.9;" style="color: green;"></span>
                            </span>
                            <div class="">
                                <input class="form-control" type="text" name="username" maxlength="20" v-model="username">
                                <span v-if="errors.username" class="alert-danger">Введите имя</span>
                            </div>
                        </div>
                        <div class="ml-md-3 mt-3 mt-md-0">
                            <span class="d-block">
                                E-mail:  
                                <span v-if="!email" class="uk-animation-shake" uk-icon="icon: ban; ratio: 0.7;" style="color: red;"></span>
                                <span v-if="email" class="uk-animation-shake" uk-icon="icon: check; ratio: 0.9;" style="color: green;"></span>
                            </span>
                            <div class="">
                                <input class="form-control" type="email" id="email" name="email" v-model="email">
                                <span v-if="errors.email" class="alert-danger">{{ errors.email[0] }}</span>
                            </div>
                        </div>
                        <div class="ml-md-3 mt-3 mt-md-0">
                            <span class="d-block">
                                Заголовок:  
                                <span v-if="!title" class="uk-animation-shake" uk-icon="icon: ban; ratio: 0.7;" style="color: red;"></span>
                                <span v-if="title" class="uk-animation-shake" uk-icon="icon: check; ratio: 0.9;" style="color: green;"></span>
                            </span>
                            <div class="">
                                <input class="form-control" type="text" name="title" v-model="title">
                                <span v-if="errors.title" class="alert-danger">Введите заголовок</span>
                            </div>
                        </div>
                        <div class="ml-md-3 mt-3 mt-md-0">
                            <span class="d-block">
                                Комментарий:  
                                <span v-if="!description" class="uk-animation-shake" uk-icon="icon: ban; ratio: 0.7;" style="color: red;"></span>
                                <span v-if="description" class="uk-animation-shake" uk-icon="icon: check; ratio: 0.9;" style="color: green;"></span>
                            </span>
                            <div class="">
                                <textarea class="form-control" type="text" name="description" v-model="description"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="uk-inline uk-align-right">
                           <button type="submit" class="btn btn-sm btn-success" :disabled="!username || !email || !title || !description || spinner == true" @click.prevent="checkForm()"><span v-if="spinner == false">Сохранить</span><span v-if="spinner == true"><div uk-spinner="ratio: 0.3" style="width: 70px; height: 13px"></div></span></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div v-for="d in data" class="uk-card uk-card-default uk-margin">
                    <div class="uk-card-header">
                        <div class="uk-grid-small uk-flex-middle" uk-grid>
                            <div class="uk-width-expand">
                                <h4 class="uk-card-title uk-margin-remove-bottom">{{ d.username }}</h4>
                                <p class="uk-text-meta uk-margin-remove-top">{{ d.email }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="uk-card-body">
                        <h4 v-if="d.editMode !== true">{{ d.title }}</h4>
                        <input v-if="d.editMode == true" class="form-control uk-margin" type="text" name="title" v-model="d.title">
                        <p v-if="d.editMode !== true">{{ d.descriptions }}</p>
                        <textarea v-if="d.editMode == true" class="form-control uk-margin" type="text" name="description" v-model="d.descriptions"></textarea>
                        <div class="uk-inline uk-align-right">
                            <p class="uk-text-meta uk-margin-remove-top">{{ d.updated_at | moment("DD.MM.YYYY, HH:mm") }}</p>
                        </div>
                        <div v-if="d.editMode == true">
                            <span class="btn btn-sm btn-success" @click.prevent="editComment(d)">Save</span>  
                            <span class="btn btn-sm btn-secondary" @click.prevent="cancelEdit(d)">Cancel</span>
                        </div>
                        <div class="" v-if="d.editMode !== true">
                            <span class="btn btn-sm btn-secondary" @click.prevent="editComment(d)">Редактировать</span>
                            <span class="btn btn-sm btn-danger mt-2 mt-md-0" @click.prevent="destroyComment(d.id)">Удалить</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
    
    .error-msg {
        background: #ff000082;
        border-left: 3px solid red;
        padding: 20px 20px;
    }

    h4 {
        font-size: 16px;
        font-weight: 700;
    }

</style>

<script>
    Vue.use(require('vue-moment'));
    export default {
        data() {
            return {
                username: '',
                email: '',
                title: '',
                description: '',
                errors: [],
                data: [],
                visibility: false,
                spinner: true,
                errors: []
            }
        },
        mounted() {
            axios
                .get('api/data')
                .then(response => (this.data = response.data.data,  this.spinner = false, this.addEditMode()))
        },
        methods: {
            makeForm() {
                this.errors = []
                this.spinner = true
                Object.assign(axios.defaults.headers, {
                    'X-Requested-With': 'XMLHttpRequest',
                });
                axios.post('store', {
                    username: this.username,
                    email: this.email,
                    title: this.title,
                    description: this.description
                })
                .then(response => {
                    this.fetchData()

                        this.username = ''
                        this.email = ''
                        this.title = ''
                        this.description = ''
                        this.visibility = false
                })
                .catch(error => {
                    if (error.response.status == 422) {
                        this.errors = error.response.data.errors
                    }
                })
                
            },
            destroyComment(id) {
                let index = this.data.findIndex(d => d.id === id)
                axios.delete('destroy/' + id)
                this.data.splice(index, 1)
            },
            editComment(d) {

                if(d.editMode == true) {
                    d.editMode = false
                    this.pushTitle(d)
                }

                else {
                    d.editMode = true
                }

            },
            cancelEdit(d) {
                if(d.editMode == true) {
                    d.editMode = false
                }
            },
            addEditMode() {
                this.data.forEach(function(item) {
                    item.editMode = false
                })
            },
            pushTitle(d) {
                axios.post('edit/' + d.id, {
                    title: d.title,
                    description: d.descriptions
                })
                .then(response => {
                    this.fetchData()
                })
            },
            fetchData() {
                axios
                    .get('api/data')
                    .then(response => (this.data = response.data.data, this.spinner = false, this.addEditMode()))
            },
            checkForm: function (e) {
              this.errors = [];

              if (!this.username) {
                this.errors.push('Укажите имя.');
              }
              if (!this.email) {
                this.errors.push('Укажите электронную почту.');
              } else if (!this.validEmail(this.email)) {
                this.errors.push('Укажите корректный адрес электронной почты.');
              }

              if (!this.errors.length) {
                this.makeForm();
              }

              e.preventDefault();
            },
            validEmail: function (email) {
              var re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
              return re.test(email);
            }
        }
    };
</script>