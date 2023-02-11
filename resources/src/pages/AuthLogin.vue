<script setup>
import { ElMessageBox } from 'element-plus';
import { useUserStore } from '@/store/user.store';
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import logoUrl from 'images/logo.png';

const { login } = useUserStore();
const $router = useRouter();

const ruleFormRef = ref();
let requestInProcess = ref(false);

const form = reactive({
  email: '',
  password: '',
});
const rules = {
  email: [
    {required: true, message: 'Поле "Email" обязательно для заполнения'},
    {type: 'email', message: 'Поле "Email" должно быть валидным email'}
  ],
  password: [
    {required: true, message: 'Поле "Пароль" обязательно для заполнения'},
    {min: 12, max: 12, message: 'Пароль должен иметь длину 12 символов'},
  ],
};

async function submitForm(formEl) {
  if (!formEl) return;
  requestInProcess.value = true;
  await formEl.validate(valid => {
    if (!valid) return;
    login(form.email, form.password).then(response => {
      $router.replace({name: 'main'})
    }).catch(error => {
      const message = error?.response?.data?.email?.[0] ?? 'Произошла ошибка при выполнении запроса';
      ElMessageBox.alert(message, 'Не удалось авторизоваться', {
        showConfirmButton: false,
        showClose: false,
        closeOnClickModal: true,
        closeOnPressEscape: true,
        center: true,
      });
    }).finally(() => {
      requestInProcess.value = false;
    });
  })
}
</script>

<template>
  <div class="pt-8 px-6 h-screen bg-slate-400 flex justify-center">
    <div class="form-container rounded-lg bg-neutral-600 w-full md:w-1/2 2xl:w-1/5 h-min py-8 px-6">

      <div class="flex flex-col mb-4">
        <div class="flex justify-center">
          <el-image :src="logoUrl" class="h-32 max-w-max"/>
        </div>
        <p class="text-2xl text-white text-center font-bold mb-2">Авторизация</p>
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
          <div class="flex justify-center">
            <el-form-item prop="password" style="width: 100%">
              <el-input placeholder="Пароль" v-model="form.password" size="large" @input="form.password = form.password.length <= 12 ? form.password : form.password.slice(0, 12)"/>
            </el-form-item>
          </div>
          <div class="flex justify-center mt-4">
            <el-button
              type="primary"
              @click="submitForm(ruleFormRef)"
              size="large"
              :loading="requestInProcess"
            >
              Войти
            </el-button>
          </div>
        </div>
      </el-form>

      <div class="bg-neutral-600 mt-4">
        <div class="flex justify-center sm:justify-around flex-col sm:flex-row">
          <router-link to="reset" class="text-green-700 hover:text-green-500 text-center p-2">Забыли пароль?</router-link>
          <router-link to="register" class="text-green-700 hover:text-green-500 text-center p-2">Регистрация</router-link>
        </div>
      </div>
    </div>
  </div>
</template>
