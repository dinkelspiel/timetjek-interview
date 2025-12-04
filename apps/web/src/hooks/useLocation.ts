import { onMounted, ref } from "vue";

export const useLocation = () => {
  const hasGeolocation = ref(false);
  const latitude = ref<number | null>(null);
  const longitude = ref<number | null>(null);

  onMounted(() => {
    navigator.geolocation.getCurrentPosition(
      async (position) => {
        latitude.value = position.coords.latitude;
        longitude.value = position.coords.longitude;
        hasGeolocation.value = true;
      },
      (geoError) => {},
      {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 0,
      }
    );
  });

  return { hasGeolocation, latitude, longitude };
};
