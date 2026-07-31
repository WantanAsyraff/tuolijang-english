<template>
  <view class="map-box" id="container" :centerPos="centerPos" :markers="markers" :circles="circles"
    :change:centerPos="hybird.handleChangeCenterPos" :change:markers="hybird.handleChangeMarkers"
    :change:circles="hybird.handleChangeCircles" />
</template>

<script>
export default {
  props: {
    longitude: String,
    latitude: String,
    markers: Array,
    circles: Array
  },
  computed: {
    centerPos() {
      return [this.longitude, this.latitude];
    }
  }
};
</script>

<script module="hybird" lang="renderjs">
export default {
  data() {
    return {
      mapInstance: null,
      key: "cf5c437b14780406af75a81b380cafac",
      securityJsCode: "60dcc64efdca6fb9bed8aeacbe21d2b8",
      version: "2.0",
      taskList: [],
      markerMap: new Map()
    }
  },
  created() {
    this.loadTask = this.loadAmapLib();
  },
  mounted() {
    if (window.AMap) {
      this.initMap();
    } else {
      this.loadTask.then(this.initMap);
    }
  },
  methods: {
    initMap() {
      const params = {
        zoom: 16,
        dragEnable: false,
        zoomEnable: false
      };

      this.mapInstance = new AMap.Map("container", params);

      let task;
      while (task = this.taskList.shift()) {
        task();
      }
    },
    handleRunTask(task) {
      if (this.mapInstance) {
        task();
      } else {
        this.taskList.push(task);
      }
    },
    handleChangeCenterPos(newCenterPos) {
      const [longitude, latitude] = newCenterPos;
      if (!longitude || !latitude) return;

      this.handleRunTask(() => {
        this.mapInstance.setCenter([longitude, latitude]);
      });
    },
    handleChangeMarkers(newMarkers) {
      if (!newMarkers.length) return;
      this.handleRunTask(() => {
        newMarkers.forEach(marker => {
          const { latitude, longitude, iconPath } = marker;

          const icon = new AMap.Icon({
            image: iconPath,
            size: new AMap.Size(26, 26),
            imageSize: new AMap.Size(26, 26)
          });

          const id = `${latitude}-${longitude}`;

          if (!this.markerMap.has(id)) {
            const markerObj = new AMap.Marker({
              position: [longitude, latitude],
              icon
            });
            this.mapInstance.add(markerObj);
            this.markerMap.set(id, 1);
          }
        });
        this.mapInstance.setFitView();
      });
    },
    handleChangeCircles(newCircles) {
      if (!newCircles.length) return;

      this.handleRunTask(() => {
        const { latitude, longitude, radius } = newCircles[0];
        const circle = new AMap.Circle({
          center: new AMap.LngLat(longitude, latitude),
          radius,
          strokeWeight: 0,
          fillColor: "#1890ff",
          fillOpacity: 0.3
        });
        this.mapInstance.add(circle);
        this.mapInstance.setFitView();
      });
    },
    loadAmapLib() {
      if (window.AMap) return;
      window._AMapSecurityConfig = {
        securityJsCode: this.securityJsCode,
      };
      return new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.onload = () => {
          resolve(true);
        };
        script.charset = "utf-8";
        script.src = `https://webapi.amap.com/maps?v=${this.version}&key=${this.key}`;
        document.head.appendChild(script);
      });
    }
  }
}
</script>

<style scoped lang="scss">
.map-box {
  width: 100vw;
  height: 459rpx;
}


</style>
