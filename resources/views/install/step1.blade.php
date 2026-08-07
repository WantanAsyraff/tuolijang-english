<!doctype html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>{{$title}} - {{$powered}}</title>
    <link rel="stylesheet" href="/install/css/install.css" />
    <link rel="stylesheet" href="/install/css/step1.css" />
    <link rel="stylesheet" href="/install/css/theme-chalk.css" />
    <script src="/install/js/vue2.6.11.js"></script>
    <script src="/install/js/element-ui.js"></script>
</head>
<body>
<div class="wrap" id="step1">
    <div class="title">
        <img class="logo" src="/install/images/oa-logo.png" alt="">
        <h1>{{ __('frontend.install.welcome') }}</h1>
        <div class="df agreement cp">
            <div class="radio-box" :class="{'is-shock': isShock}" @click="radio = !radio">
                <img v-if="radio" src="/install/images/success.png" alt="">
            </div>
            <span @click="radio = !radio">{{ __('frontend.install.read_and_agree') }}</span>
            <span class="agreements" @click.stop="isShow = 1">{{ __('frontend.install.license_name') }}</span>
        </div>
        <div class="bottom tac"> <span class="btn" :class="{'more-text': radio}" @click="jump">
              {{ __('frontend.install.start') }}</span> </div>
        <img class="solgen" src="/install/images/solgen.png" alt="">
    </div>
    <div class="section" v-if="isShow">
        <div class="main cc">
          <pre class="pact" readonly="readonly"><h1 class="title">{{ __('frontend.install.license_name') }}</h1>
{{ __('frontend.install.license') }}
</pre>
        </div>
        <div class="bottom" @click="agree">{{ __('frontend.install.acknowledge') }}</div>
    </div>
</div>
@include('install/footer')

</body>
<script>
    new Vue({
        el: '#step1',
        data() {
            return { radio: 0,isShow: 0,isShock:false }
        },
        methods:{
            jump(){
                if(this.radio==1){
                    window.location.href = "/install/index/2";
                } else {
                    this.$message({
                        message: @json(__('frontend.install.accept_first')),
                        type: 'error'
                    });
                    this.isShock = true
                    setTimeout(e=>{this.isShock = false},500)
                }
            },
            agree(){
                this.isShow = 0
            }
        }
    })
</script>
<script>
    console.log(`
 /__  ___/
   / /            ___     //           ___         ( ) ( )  ___       __      ___
  / /  //   / / //   ) ) // //   / / //   ) )     / / / / //   ) ) //   ) ) //   ) )
 / /  //   / / //   / / // //   / / //   / /     / / / / //   / / //   / / ((___/ /
/ /  ((___( ( ((___/ / // ((___( ( ((___/ / ((  / / / / ((___( ( //   / /   //__

  Tuoluojiang https://www.tuoluojiang.com/
        `)
</script>
</html>
