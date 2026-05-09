import { loadUser } from './auth';
import { userStore } from '@/utils/userStore';

export async function initUserStore() {
  const user = await loadUser();

  if (user) {
    userStore.user = {
      user_id: user.user_id,
      full_name: user.full_name,
      profile_photo_url: user.profile_photo_url,
      is_admin: user.is_admin,
      mcsl_points: user.mcsl_points || 0,
    };
  }
}