<script setup>
import { ElMessageBox } from 'element-plus';
import { useUserStore } from '@/store/user.store';
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import logoUrl from 'images/logo.png';

const { register } = useUserStore();
const $router = useRouter();
const ruleFormRef = ref();
let requestInProcess = ref(false);

const form = reactive({
  email: '',
});
const rules = {
  email: [
    {required: true, message: 'Поле "Email" обязательно для заполнения'},
    {type: 'email', message: 'Поле "Email" должно быть валидным email'}
  ],
};

async function submitForm(formEl) {
  if (!formEl) return;
  requestInProcess.value = true;
  await formEl.validate(valid => {
    if (!valid) return;
    register(form.email).then(response => {
      if (response.status !== 200) {
        ElMessageBox.alert('Произошла ошибка при выполнении запроса', 'Произошла ошибка при выполнении запроса', {
          showConfirmButton: false,
          showClose: false,
          closeOnClickModal: true,
          closeOnPressEscape: true,
          center: true,
        });
      } else {
        ElMessageBox.alert('Пароль для входа был отправлен на указанный email', '', {
          showClose: false,
          callback: function () {
            $router.replace({name: 'login'});
          },
          center: true,
        });
      }
    }).catch(error => {
      const message = error?.response?.data?.email?.[0] ?? 'Произошла ошибка при выполнении запроса';
      if (error.response.status === 422) {
        ElMessageBox.alert(message, 'Ошибка сброса пароля', {
          showConfirmButton: false,
          showClose: false,
          closeOnClickModal: true,
          closeOnPressEscape: true,
          center: true,
        });
      }
    }).finally(() => {
      requestInProcess.value = false;
    });
  });
}
</script>

<template>
  <div class="pt-8 px-6 h-screen bg-slate-400 flex justify-center">
    <div class="form-container rounded-lg bg-neutral-600 w-full md:w-1/2 2xl:w-1/5 h-min py-8 px-6">

      <div class="flex flex-col mb-4">
        <div class="flex justify-center">
          <el-image :src="logoUrl" class="h-32 max-w-max"/>
        </div>
        <p class="text-2xl text-white text-center font-bold mb-2">Регистрация</p>
      </div>

      <el-form
        ref="ruleFormRef"
        :model="form"
        :rules="rules"
      >
        <div class="flex flex-col">
          <div class="flex justify-center">
            <el-form-item prop="email" style="width: 100%">
              <el-input placeholder="Email" v-model="form.email" size="large"/>
            </el-form-item>
          </div>
          <div class="flex justify-center mt-4">
            <el-button
              type="primary"
              @click="submitForm(ruleFormRef)"
              size="large"
              :loading="requestInProcess"
            >
              Зарегистрироваться
            </el-button>
          </div>
        </div>
      </el-form>

      <div class="bg-neutral-600 mt-4">
        <div class="flex justify-center sm:justify-around flex-col sm:flex-row">
          <router-link to="reset" class="text-green-700 hover:text-green-500 text-center p-2">Забыли пароль?</router-link>
          <router-link to="login" class="text-green-700 hover:text-green-500 text-center p-2">Авторизация</router-link>
        </div>
      </div>
    </div>
  </div>
</template>
