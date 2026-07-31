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
}
</script>

<script module="hybird" lang="renderjs">
export default {
  data() {
    return {
      mapInstance: null,
      key: "9014f275e138bedb410bb8f1f962e03e",
      taskList: [],
      markerMap: new Map(),
      lngLatList: []
    }
  },
  created() {
    this.loadTask = this.loadTMapLib();
  },
  mounted() {
    if (window.T) {
      this.initMap();
    } else {
      this.loadTask.then(this.initMap);
    }
  },
  methods: {
    handleFitMapView() {
      this.lngLatList.length > 1 && this.mapInstance.setViewport(this.lngLatList.map(lngLat => new T.LngLat(...lngLat)));
    },
    initMap() {
      this.mapInstance = new T.Map('container');

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
        const lnglat = new T.LngLat(longitude, latitude);
        this.mapInstance.centerAndZoom(lnglat, 13);
      });
    },
    handleChangeMarkers(newMarkers) {
      if (!newMarkers.length) return;
      this.handleRunTask(() => {
        newMarkers.forEach(markerInfo => {
          const { latitude, longitude, iconPath } = markerInfo;

          const id = `${latitude}-${longitude}`;

          if (!this.markerMap.has(id)) {
            const icon = new T.Icon({
              iconUrl: iconPath,
              iconSize: new T.Point(26, 26),
            });

            const marker = new T.Marker(
              new T.LngLat(longitude, latitude),
              {
                icon
              }
            );

            this.mapInstance.addOverLay(marker);

            this.markerMap.set(id, 1);

            this.lngLatList.push([longitude, latitude]);
            this.handleFitMapView();
          }
        });
      });
    },
    handleChangeCircles(newCircles) {
      if (!newCircles.length) return;

      this.handleRunTask(() => {
        const { latitude, longitude, radius } = newCircles[0];
        const circle = new T.Circle(
          new T.LngLat(longitude, latitude),
          radius,
          {
            fillColor: "#1890ff",
            fillOpacity: 0.3,
            weight: 0
          }
        );
        this.mapInstance.addOverLay(circle);
      });
    },
    loadTMapLib() {
      if (window.T) return;
      return new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.onload = () => {
          resolve(true);
        };
        script.src = `https://api.tianditu.gov.cn/api?v=4.0&tk=${this.key}`;
        document.head.appendChild(script);
      });
    }
  }
}
</script>

<style scoped lang="scss">
.map-box {
  width: 100vw;
  height: 40vh;

  :deep(.tdt-bottom) {
    z-index: 700;
  }
}
</style>
