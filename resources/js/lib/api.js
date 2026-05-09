import axios from "axios";
import { usePage } from "@inertiajs/vue3";

const instance = axios.create();

// Request Interceptor → setzt BaseURL automatisch
instance.interceptors.request.use((config) => {
  const page = usePage();

  const baseURL = page.props.app_url;

  config.baseURL = baseURL;

  return config;
});

export default instance;
