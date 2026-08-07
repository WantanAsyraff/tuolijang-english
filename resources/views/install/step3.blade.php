<!doctype html>
<html>
<head>
    <meta charset="UTF-8"/>
    <title>{{$Title}} - {{$Powered}}</title>
    <link rel="stylesheet" href="/install/css/install.css" />
    <link rel="stylesheet" href="/install/css/step3.css" />
    <link rel="stylesheet" href="/install/css/theme-chalk.css" />
    <script src="/install/js/vue2.6.11.js"></script>
    <script src="/install/js/element-ui.js"></script>
    <script src="/install/js/jquery.js"></script>
</head>
<body>
<div class="wrap" id="step3" v-cloak>
    <div class="title">
        {{ __('frontend.install.create_data') }}
    </div>
    <section class="section">
        <div class="server"  ref="mianscroll">
            <el-form :model="form" :rules="rules" ref="ruleForm" label-width="120px">
                <p class="tip">{{ __('frontend.install.database_info') }}</p>
                <el-form-item label="{{ __('frontend.install.database_user') }}" prop="dbUser" class="label" :error="errorMsg.db_user">
                    <el-input v-model="form.dbUser" placeholder="{{ __('frontend.install.placeholder_database_user') }}"></el-input>
                </el-form-item>
                <el-form-item label="{{ __('frontend.install.database_password') }}" prop="dbPwd" class="label" :error="errorMsg.db_pwd">
                    <el-input v-model="form.dbPwd" type="password" placeholder="{{ __('frontend.install.placeholder_database_password') }}" show-password></el-input>
                </el-form-item>
                <el-form-item label="{{ __('frontend.install.database_name') }}" prop="dbName" class="label" :error="errorMsg.db_name">
                    <el-input v-model="form.dbName" placeholder="{{ __('frontend.install.placeholder_database_name') }}"></el-input>
                </el-form-item>
                <el-form-item label="{{ __('frontend.install.advanced') }}" class="label">
                    <el-switch v-model="value" active-color="#37CA71" inactive-color="#575869"></el-switch>
                </el-form-item>
                <el-form-item label="{{ __('frontend.install.database_host') }}" prop="dbHost" class="label" v-if="value" :error="errorMsg.db_host">
                    <el-input v-model="form.dbHost" placeholder="{{ __('frontend.install.placeholder_database_host') }}"></el-input>
                </el-form-item>
                <el-form-item label="{{ __('frontend.install.database_port') }}" prop="dbPort" class="label" v-if="value" :error="errorMsg.db_port">
                    <el-input v-model="form.dbPort" placeholder="{{ __('frontend.install.placeholder_database_port') }}"></el-input>
                </el-form-item>
                <el-form-item label="{{ __('frontend.install.database_prefix') }}" prop="dbPrefix" class="label" v-if="value" :error="errorMsg.db_prefix">
                    <el-input v-model="form.dbPrefix" placeholder="{{ __('frontend.install.placeholder_database_prefix') }}"></el-input>
                </el-form-item>
                <el-form-item label="{{ __('frontend.install.demo_data') }}" class="label" v-if="value">
                    <el-checkbox v-model="form.initData"></el-checkbox>
                </el-form-item>
                <p class="tip">{{ __('frontend.install.admin_info') }}</p>
                <el-form-item label="{{ __('frontend.install.admin_account') }}" prop="account" class="label" :error="errorMsg.account">
                    <el-input v-model="form.account" placeholder="{{ __('frontend.install.placeholder_admin_account') }}"></el-input>
                </el-form-item>
                <el-form-item label="{{ __('frontend.install.admin_password') }}" prop="password" class="label" :error="errorMsg.password">
                    <el-input v-model="form.password" type="password" placeholder="{{ __('frontend.install.placeholder_admin_password') }}" show-password></el-input>
                </el-form-item>
                <el-form-item label="{{ __('frontend.install.repeat_password') }}" prop="checkPass" class="label" :error="errorMsg.check_pass">
                    <el-input v-model="form.checkPass" type="password" placeholder="{{ __('frontend.install.placeholder_repeat_password') }}" show-password></el-input>
                </el-form-item>
                <p class="tip">{{ __('frontend.install.cache_settings') }}</p>
                <el-form-item label="{{ __('frontend.install.cache_method') }}" class="label">
                    <el-radio-group v-model="form.cacheDriver" class="ml-4">
{{--                        <el-radio label="file" name="cache_type" id="cache_type1">文件缓存</el-radio>--}}
                        <el-radio label="redis" name="cache_type" id="cache_type2">{{ __('frontend.install.redis_cache') }}</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item label="{{ __('frontend.install.server_address') }}" prop="rbHost" class="label" v-if="form.cacheDriver === 'redis'" :error="errorMsg.redis_host">
                    <el-input v-model="form.rbHost" placeholder="{{ __('frontend.install.placeholder_redis_host') }}"></el-input>
                </el-form-item>
                <el-form-item label="{{ __('frontend.install.port') }}" prop="rbPort" class="label" v-if="form.cacheDriver === 'redis'" :error="errorMsg.redis_port">
                    <el-input v-model="form.rbPort" placeholder="{{ __('frontend.install.placeholder_redis_port') }}"></el-input>
                </el-form-item>
                <el-form-item label="{{ __('frontend.install.database') }}" prop="rbNum" class="label" v-if="form.cacheDriver === 'redis'" :error="errorMsg.redis_db">
                    <el-input v-model="form.rbNum" placeholder="{{ __('frontend.install.placeholder_redis_database') }}"></el-input>
                </el-form-item>
                <el-form-item label="{{ __('frontend.install.database_password') }}" class="label" v-if="form.cacheDriver === 'redis'" :error="errorMsg.redis_pwd">
                    <el-input v-model="form.rbPwd" type="password" placeholder="{{ __('frontend.install.placeholder_redis_password') }}" show-password></el-input>
                </el-form-item>
            </el-form>
        </div>
        <div class="bottom-btn">
            <div class="bottom tac up-btn">
                <a href="/install/index/2" class="btn">{{ __('frontend.install.previous') }}</a>
            </div>
            <div class="bottom tac next">
                <a @click="submitForm('ruleForm')" class="btn" v-loading="loading">{{ __('frontend.install.next') }}</a>
            </div>
        </div>
    </section>
    <div style="width:0;height:0;overflow:hidden;"><img src="/install/images/pop_loading.gif"></div>
    <script>
        const form = <?php echo json_encode($form); ?>;
        new Vue({
            el: '#step3',
            data() {
                const validatePass = (rule, value, callback) => {
                    if (value === '') {
                        callback(new Error(@json(__('frontend.install.password_required'))));
                    } else if (value.length < 6) {
                        callback(new Error(@json(__('frontend.install.password_min'))));
                    } else {
                        if (this.form.checkPass !== '') {
                            this.$refs.ruleForm.validateField('checkPass');
                        }
                        callback();
                    }
                };
                const validatePass2 = (rule, value, callback) => {
                    if (value === '') {
                        callback(new Error(@json(__('frontend.install.password_repeat'))))
                    } else if (value !== this.form.password) {
                        callback(new Error(@json(__('frontend.install.password_mismatch'))))
                    } else {
                        callback()
                    }
                }
                const validatePort = (rule, value, callback) => {
                    const port = Number(value)
                    if (!/^\d+$/.test(String(value)) || port < 1 || port > 65535) {
                        callback(new Error(@json(__('frontend.install.invalid_port'))))
                    } else {
                        callback()
                    }
                }
                const validateDbPrefix = (rule, value, callback) => {
                    if (!/^[A-Za-z][A-Za-z0-9_]*_$/.test(value)) {
                        callback(new Error(@json(__('frontend.install.invalid_prefix'))))
                    } else {
                        callback()
                    }
                }
                const validateRedisDb = (rule, value, callback) => {
                    const db = Number(value)
                    if (!/^\d+$/.test(String(value)) || db < 0 || db > 15) {
                        callback(new Error(@json(__('frontend.install.invalid_redis_database'))))
                    } else {
                        callback()
                    }
                }
                return {
                    loading: false,
                    value: false,
                    radio: 0,
                    form: form,
                    rules:{
                        dbUser:[{ required: true, message: @json(__('frontend.install.placeholder_database_user')), trigger: 'blur' },],
                        dbHost:[{ required: true, message: @json(__('frontend.install.placeholder_database_host')), trigger: 'blur' },],
                        dbPort:[{ required: true, message: @json(__('frontend.install.placeholder_database_port')), trigger: 'blur' },{ validator: validatePort, trigger: 'blur' }],
                        dbName:[{ required: true, message: @json(__('frontend.install.placeholder_database_name')), trigger: 'blur' },],
                        dbPwd:[{ required: true, message: @json(__('frontend.install.placeholder_database_password')), trigger: 'blur' },],
                        dbPrefix:[{ required: true, message: @json(__('frontend.install.placeholder_database_prefix')), trigger: 'blur' },{ validator: validateDbPrefix, trigger: 'blur' }],
                        rbHost:[{ required: true, message: @json(__('frontend.install.placeholder_redis_host')), trigger: 'blur' },],
                        rbPort:[{ required: true, message: @json(__('frontend.install.placeholder_redis_port')), trigger: 'blur' },{ validator: validatePort, trigger: 'blur' }],
                        rbNum:[{ required: true, message: @json(__('frontend.install.placeholder_redis_database')), trigger: 'blur' },{ validator: validateRedisDb, trigger: 'blur' }],
                        account:[{ required: true, message: @json(__('frontend.install.placeholder_admin_account')), trigger: 'blur' },{ validator: this.validateMobile, trigger: 'blur' },],
                        password:[{ validator: validatePass, trigger: 'blur' }],
                        checkPass: [{ required: true, message: @json(__('frontend.install.password_repeat')), trigger: 'blur' },{ validator: validatePass2, trigger: 'blur' }],
                    },
                    errorMsg:{
                        db_host:'',
                        db_port:'',
                        db_name:'',
                        db_prefix:'',
                        db_user:'',
                        db_pwd:'',
                        account:'',
                        password:'',
                        check_pass:'',
                        redis_host:'',
                        redis_port:'',
                        redis_db:'',
                        redis_pwd:'',
                    }
                }
            },
            methods: {
                mysqlDbPwd() {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "POST",
                            url: '/install/index/3',
                            data: this.form,
                            dataType: 'JSON',
                            success: (msg) => {
                                resolve(msg);
                            },
                            error: (err) => {
                                reject(err)
                            }
                        });
                    })
                },
                validateMobile(rule, value, callback) {
                    const normalized = String(value || '').replace(/[\s()-]/g, '');
                    const reg = /^\+?[1-9]\d{6,14}$/;
                    if (value === '' || reg.test(normalized)) {
                        callback();
                    } else {
                        callback(new Error(@json(__('frontend.install.invalid_mobile'))));
                    }
                },
                jumpButton(){
                    this.$refs.mianscroll.scrollTop = this.$refs.mianscroll.clientHeight
                },
                submitForm(formName) {
                    if (this.loading){
                        return;
                    }
                    this.$refs[formName].validate((valid) => {
                        if (valid) {
                            this.loading = true
                            this.resetError()
                            this.mysqlDbPwd().then(res=>{
                                let code = Number(res.data.code)
                                this.loading = false
                                let hasError = false
                                switch (code){
                                    case 2002:
                                        this.errorMsg.db_host = @json(__('frontend.install.database_connection'))
                                        this.errorMsg.db_port = @json(__('frontend.install.database_connection'))
                                        hasError = true
                                        break;
                                    case 1045:
                                        this.errorMsg.db_user = @json(__('frontend.install.database_credentials'))
                                        this.errorMsg.db_pwd = @json(__('frontend.install.database_credentials'))
                                        hasError = true
                                        break;
                                    case 1049:
                                        this.errorMsg.db_name = @json(__('frontend.install.database_missing'))
                                        hasError = true
                                        break;
                                    case -5:
                                        this.errorMsg.db_name = @json(__('frontend.install.mysql_version'))
                                        hasError = true
                                        break;
                                    case -4:
                                        this.errorMsg.db_name = @json(__('frontend.install.database_not_empty'))
                                        hasError = true
                                        break;
                                    case -3:
                                        this.errorMsg.redis_pwd = @json(__('frontend.install.redis_connection'))
                                        hasError = true
                                        break;
                                    case -2:
                                        this.errorMsg.account = @json(__('frontend.install.invalid_mobile'))
                                        hasError = true
                                        break;
                                    case -1:
                                        this.errorMsg.redis_host = @json(__('frontend.install.redis_extension'))
                                        hasError = true
                                        break;
                                    case -6:
                                    case -10:
                                        hasError = true
                                        break;
                                    case 1:
                                        window.location.href = '/install/index/4'
                                        break;
                                    case 61:
                                        this.errorMsg.redis_host = 'Connection failed: Connection refused!'
                                        hasError = true
                                        break;
                                    case 60:
                                        this.errorMsg.redis_host = 'Connection failed: Operation timed out!'
                                        hasError = true
                                        break;
                                    case 0:
                                        this.errorMsg.redis_host = 'Connection failed: `AUTH` failed!'
                                        hasError = true
                                        break;
                                    default:
                                        if (code !== undefined && code !== 1) {
                                            hasError = true
                                        }
                                }
                                // 优先使用后端返回的错误消息覆盖默认提示
                                if (hasError && res.data.msg) {
                                    const fieldMap = {
                                        dbHost: 'db_host',
                                        dbPort: 'db_port',
                                        dbName: 'db_name',
                                        dbUser: 'db_user',
                                        dbPwd: 'db_pwd',
                                        dbPrefix: 'db_prefix',
                                        account: 'account',
                                        password: 'password',
                                        checkPass: 'check_pass',
                                        rbHost: 'redis_host',
                                        rbPort: 'redis_port',
                                        rbNum: 'redis_db',
                                        rbPwd: 'redis_pwd'
                                    }
                                    if (res.data.field && fieldMap[res.data.field]) {
                                        this.errorMsg[fieldMap[res.data.field]] = res.data.msg
                                    } else if (code === -2) {
                                        this.errorMsg.account = res.data.msg
                                    } else if (code === -3 || code === 61 || code === 60 || code === 0 || code === -1) {
                                        this.errorMsg.redis_host = res.data.msg
                                        this.errorMsg.redis_pwd = res.data.msg
                                    } else if (code === -5 || code === -4 || code === 1049) {
                                        this.errorMsg.db_name = res.data.msg
                                    } else if (code === 1045) {
                                        this.errorMsg.db_user = res.data.msg
                                        this.errorMsg.db_pwd = res.data.msg
                                    } else if (code === 2002) {
                                        this.errorMsg.db_host = res.data.msg
                                        this.errorMsg.db_port = res.data.msg
                                    } else {
                                        // 未匹配的错误码，显示在数据库名称字段
                                        this.errorMsg.db_name = res.data.msg
                                    }
                                }
                            }).catch(err=>{
                                this.loading = false
                                this.$message.error(@json(__('frontend.install.request_failed')))
                            })
                        } else {
                            console.log('error submit!!');
                            return false;
                        }
                    });
                },
                resetError(){
                    this.errorMsg.db_host = ''
                    this.errorMsg.db_port = ''
                    this.errorMsg.db_name = ''
                    this.errorMsg.db_prefix = ''
                    this.errorMsg.db_user = ''
                    this.errorMsg.db_pwd = ''
                    this.errorMsg.account = ''
                    this.errorMsg.password = ''
                    this.errorMsg.check_pass = ''
                    this.errorMsg.redis_host = ''
                    this.errorMsg.redis_port = ''
                    this.errorMsg.redis_db = ''
                    this.errorMsg.redis_pwd = ''
                }
            }
        })


    </script>
</div>
@include('install/footer')
</body>
<style>
    .server .label{
        color: #fff;
        margin-bottom: 0;
    }
    .tip{
        font-size: 14px;
        width: 105px;
        text-align: right;
        color: #fff;
        padding-right: 15px;
    }
    .el-form-item {
        height: 50px;
        line-height: 50px;
    }
    .el-form-item__label{
        font-weight: normal;
        text-align: right;
        padding: 2px 15px;
        color: #fff;
        font-size: 12px;
        width: 120px;
    }
    .el-input{
        width: 200px;
        height: 30px;
    }
    .el-input__inner{
        border: none;
        border-radius: 20px;
        background-color: rgba(0,0,0,0.1);
        padding: 0px 15px;
        width: 200px;
        height: 30px;
        color: #fff;
    }
    .el-form-item__error{
        position: relative;
        font-size: 12px;
        top: -30px;
        left: 210px;
    }
    .el-loading-mask{
        border-radius: 137px;
        font-size: 14px;
    }
    .el-loading-spinner{
        margin-top: -15px;
    }
    .el-loading-spinner .circular{
        width: 30px;
        height: 30px;
    }
</style>
</html>
