<!doctype html>
<html>
<head>
    <meta charset="UTF-8"/>
    <title>{{$Title}} - {{$Powered}}</title>
    <link rel="stylesheet" href="/install/css/install.css" />
    <link rel="stylesheet" href="/install/css/step4.css" />
    <link rel="stylesheet" href="/install/css/theme-chalk.css" />
    <script src="/install/js/vue2.6.11.js"></script>
    <script src="/install/js/element-ui.js"></script>
    <script src="/install/js/jquery.js"></script>
</head>
<body>
<div class="wrap" id="step4">
    <div class="title">
        {{ __('frontend.install.progress') }}
    </div>
    <section class="section">
        <div class="title">
            <h1 v-text="statusTitle"></h1>
        </div>
        <div class="progress">
            <el-progress :percentage="percentage" color="#37CA71" define-back-color="rgba(255,255,255,0.5)" :stroke-width="8" :status="progressStatus"></el-progress>
            <div class="progress-msg" v-if="!isShow">
                <div id="loginner_item" class="msg p8" v-text="currentMsg"></div>
                <!--                <div class="open" @click="openList">查看详情</div>-->
            </div>
        </div>
        <div class="install" ref="install" id="log" v-show="isShow">
            <div id="loginner" class="item" v-for="(item,index) in installList" :key="index">
                <span v-text="item.msg"></span>
                <span v-text="item.time"></span>
            </div>
        </div>
        <div class="bottom tac"><a href="javascript:;" class="btn_old mid" @click="handleAction">
            <img class="shuaxin" v-if="!failed && !finished" src="/install/images/shuaxin.png"/>&nbsp;<span v-text="buttonText"></span></a>
        </div>
    </section>
</div>
@include('install/footer')
</body>
<script type="text/javascript">
    $.ajaxSetup({cache: false});
    new Vue({
        el: '#step4',
        data() {
            return {
                percentage: 0,
                isShow: 0,
                installList: [],
                n:0,
                failed: false,
                finished: false,
                currentMsg: @json(__('frontend.install.preparing'))
            }
        },
        computed: {
            statusTitle() {
                if (this.failed) {
                    return @json(__('frontend.install.failed_retry'))
                }
                if (this.finished) {
                    return @json(__('frontend.install.complete_redirect'))
                }
                return @json(__('frontend.install.installing_wait'))
            },
            progressStatus() {
                return this.failed ? 'exception' : 'success'
            },
            buttonText() {
                if (this.failed) {
                    return @json(__('frontend.install.reinstall'))
                }
                if (this.finished) {
                    return @json(__('frontend.install.complete'))
                }
                return @json(__('frontend.install.installing'))
            }
        },
        mounted() {
            this.reloads(this.n);
        },
        methods: {
            reloads(num) {
                if (this.failed || this.finished) {
                    return;
                }
                var url = location.href+'?n='+num;
                $.ajax({
                    type: "POST",
                    url: url,
                    data: {n:num},
                    dataType: 'JSON',
                    cache: false,
                    beforeSend: () => {
                    },
                    success: (res) => {
                        if (!res || !res.data) {
                            this.handleFail(@json(__('frontend.install.invalid_response')))
                            return
                        }
                        const count = Number(res.data.count) > 0 ? Number(res.data.count) : 1
                        const n = Number(res.data.n) || 0
                        this.percentage = Math.round((n / count) * 100) > 100 ? 100 : Math.round((n / count) * 100)

                        if (res.data.msg){
                            this.currentMsg = res.data.msg
                            this.installList.push({
                                msg: res.data.msg,
                                time: res.data.time
                            })
                            this.$nextTick(e => {
                                this.$refs.install.scrollTop = this.$refs.install.scrollHeight;
                            })
                        }
                        if (res.data.error) {
                            this.handleFail(res.data.msg || @json(__('frontend.install.failed_log')))
                            return false
                        }

                        if (n >= 0) {
                            if (n === 99999) {
                                this.finished = true
                                this.percentage = 100
                                setTimeout(e => {
                                    this.gonext()
                                }, 1000);
                                return false;
                            } else {
                                this.reloads(n);
                            }
                        } else {
                            this.handleFail(res.data.msg || @json(__('frontend.install.failed_config')))
                        }

                    },
                    error: () => {
                        this.handleFail(@json(__('frontend.install.api_failed')))
                    }
                });
            },
            openList() {
                this.isShow = true
                this.$nextTick(e => {
                    this.$refs.install.scrollTop = this.$refs.install.scrollHeight;
                })
            },
            handleFail(msg) {
                this.failed = true
                this.currentMsg = msg
                this.installList.push({
                    msg: msg,
                    time: new Date().toLocaleString()
                })
                this.$message.error(msg)
            },
            handleAction() {
                if (this.failed) {
                    window.location.href = '/install/index/4'
                }
            },
            gonext() {
                window.location.href = '/install/index/5';
            }
        }
    })
</script>
</html>
