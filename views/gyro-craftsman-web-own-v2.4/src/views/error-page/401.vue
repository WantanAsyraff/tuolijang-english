<template>
  <div class="errPage-container">
    <el-button icon="el-icon-arrow-left" type="primary" @click="$router.back()"> {{ $("access.return") }} </el-button>
    <el-row>
      <el-col :span="12">
        <h1 class="text-jumbo text-ginormous">Oops!</h1>
        <h2>{{ $("legacy.92a9abf2f57b9c19") }}</h2>
        <h6>{{ $("legacy.43d16dc5680e51e8") }}</h6>
        <ul class="list-unstyled">
          <li>{{ $("legacy.e4910bef1623948d") }}</li>
          <li class="link-type">
            <p @click="back(1)">{{ $("legacy.32a05626b83cf41c") }}</p>
          </li>
          <li><a href="#" @click.prevent="dialogVisible = true">{{ $("legacy.ae844dd13514b784") }}</a></li>
        </ul>
      </el-col>
      <el-col :span="12">
        <img :src="errGif" width="313" height="428" alt="Girl has dropped her ice cream." />
      </el-col>
    </el-row>
    <el-dialog :visible.sync="dialogVisible" :title='$("legacy.575e09714ad8ed99")'>
      <img :src="ewizardClap" class="pan-img" />
    </el-dialog>
  </div>
</template>

<script>
import errGif from '@/assets/401_images/401.gif';

export default {
  name: 'Page401',
  data() {
    return {
      errGif: errGif + '?' + +new Date(),
      ewizardClap: 'https://wpimg.wallstcn.com/007ef517-bafd-4066-aae4-6883632d9646',
      dialogVisible: false,
    };
  },
  methods: {
    back(type) {
      if (type === 1) {
        this.$router.push({ name: 'dashboard' });
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.errPage-container {
  width: 800px;
  max-width: 100%;
  margin: 100px auto;
  .pan-back-btn {
    background: #008489;
    color: #fff;
    border: none !important;
  }
  .pan-gif {
    margin: 0 auto;
    display: block;
  }
  .pan-img {
    display: block;
    margin: 0 auto;
    width: 100%;
  }
  .text-jumbo {
    font-size: 60px;
    font-weight: 700;
    color: #484848;
  }
  .list-unstyled {
    font-size: 14px;
    li {
      padding-bottom: 5px;
    }
    a {
      color: #008489;
      text-decoration: none;
      &:hover {
        text-decoration: underline;
      }
    }
  }
}
</style>
