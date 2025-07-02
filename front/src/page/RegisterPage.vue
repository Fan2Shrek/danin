<script setup lang="ts">
import { ref, computed, reactive } from 'vue';
import { z } from 'zod';
import { usePasswordStrength } from '@/composables/usePasswordStrength';
import api from '@/lib/api/api';
import tokens from '@/i18n/tokens';

// Password strength composable
const { password, strengthResult, isPasswordStrong } = usePasswordStrength();

// Form data
const formData = reactive({
  username: '',
  email: '',
  confirmPassword: ''
});

// Form state
const isSubmitting = ref(false);
const submitError = ref('');
const submitSuccess = ref(false);

// Validation schema
const registrationSchema = z.object({
  username: z.string()
    .min(3, tokens.register.error.username.minLength)
    .max(20, tokens.register.error.username.maxLength)
    .regex(/^[a-zA-Z0-9_]+$/, tokens.register.error.username.regex),
  email: z.string()
    .email(tokens.register.error.email.invalid),
  password: z.string()
    .min(8, tokens.register.error.password.minLength),
  confirmPassword: z.string()
}).refine(data => data.password === data.confirmPassword, {
  message: tokens.register.error.confirmPassword.mismatch,
  path: ["confirmPassword"]
});

// Form validation errors
const validationErrors = ref<Record<string, string>>({});

// Validate form
const validateForm = () => {
  const formValues = {
    username: formData.username,
    email: formData.email,
    password: password.value,
    confirmPassword: formData.confirmPassword
  };

  try {
    registrationSchema.parse(formValues);
    validationErrors.value = {};

    return true;
  } catch (error) {
    if (error instanceof z.ZodError) {
      const errors: Record<string, string> = {};
      error.errors.forEach(err => {
        if (err.path[0]) {
          errors[err.path[0] as string] = err.message;
        }
      })
      validationErrors.value = errors;
    }

    return false;
  }
}

// Check if form is valid
const isFormValid = computed(() => {
  const hasAllFields = formData.username && password.value && formData.confirmPassword;
  const hasStrongPassword = isPasswordStrong.value;

  // Validate silently to check current state
  const formValues = {
    username: formData.username,
    email: formData.email,
    password: password.value,
    confirmPassword: formData.confirmPassword
  };

  try {
    registrationSchema.parse(formValues);

    return hasAllFields && hasStrongPassword;
  } catch {
    return false;
  }
});

const handleSubmit = async () => {
  if (!validateForm()) {
    return;
  }

  if (!isPasswordStrong.value) {
    submitError.value = tokens.register.error.password.notStrong;

    return;
  }

  isSubmitting.value = true;
  submitError.value = '';

  try {
    const registrationData = {
      username: formData.username,
      email: formData.email,
      password: password.value,
      passwordConfirmation: formData.confirmPassword
    };

    await api().user().register(registrationData);

    submitSuccess.value = true;
    // Reset form
    formData.username = '';
    formData.email = '';
    password.value = '';
    formData.confirmPassword = '';
    validationErrors.value = {};
  } catch (error) {
    if (error instanceof Error) {
      if (error.message.includes('Username already exists')) {
        submitError.value = tokens.register.error.username.alreadyExists;
      } else {
        submitError.value = error.message;
      }
    } else {
      submitError.value = tokens.register.error.miscellaneous;
    }
  } finally {
    isSubmitting.value = false;
  }
}

const validateField = (field: string) => {
  const formValues = {
    username: formData.username,
    email: formData.email,
    password: password.value,
    confirmPassword: formData.confirmPassword
  };

  try {
    registrationSchema.parse(formValues);

    // If validation passes, remove any existing error for this field
    if (validationErrors.value[field]) {
      delete validationErrors.value[field];
    }
  } catch (error) {
    if (error instanceof z.ZodError) {
      const fieldError = error.errors.find(err => err.path[0] === field);

      if (fieldError) {
        validationErrors.value[field] = fieldError.message;
      }
    }
  }
}
</script>

<template>
  <div class="registration-container">
    <div class="registration-card">
      <h1 class="title">{{ $t(tokens.register.title) }}</h1>
      <p class="subtitle">{{ $t(tokens.register.subtitle) }}</p>
      
      <!-- Success message -->
      <div v-if="submitSuccess" class="success-message">
        <svg class="success-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        {{ $t(tokens.register.success) }}
      </div>

      <!-- Error message -->
      <div v-if="submitError" class="error-message">
        <svg class="error-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        {{ $t(submitError) }}
      </div>

      <form @submit.prevent="handleSubmit" class="form" v-if="!submitSuccess">
        <!-- Username -->
        <div class="form-group">
          <label for="username" class="label">{{ $t(tokens.user.username) }} <span class="red">*</span></label>
          <input
            id="username"
            type="text"
            v-model="formData.username"
            @blur="validateField('username')"
            :class="['input', { 'input-error': validationErrors.username }]"
            :placeholder="$t(tokens.register.placeholder.username)"
          />
          <div v-if="validationErrors.username" class="field-error">
            {{ $t(validationErrors.username) }}
          </div>
        </div>

        <!-- Email -->
        <div class="form-group">
          <label for="email" class="label">{{ $t(tokens.user.email) }} <span class="red">*</span></label>
          <input
            id="email"
            type="text"
            v-model="formData.email"
            @blur="validateField('email')"
            :class="['input', { 'input-error': validationErrors.email }]"
            :placeholder="$t(tokens.register.placeholder.email)"
          />
          <div v-if="validationErrors.email" class="field-error">
            {{ $t(validationErrors.email) }}
          </div>
        </div>

        <!-- Password -->
        <div class="form-group">
          <label for="password" class="label">{{ $t(tokens.user.password) }} <span class="red">*</span></label>
          <input
            id="password"
            type="password"
            v-model="password"
            @blur="validateField('password')"
            :class="['input', { 'input-error': validationErrors.password }]"
            :placeholder="$t(tokens.register.placeholder.password)"
          />
          <div v-if="validationErrors.password" class="field-error">
            {{ $t(validationErrors.password) }}
          </div>
          
          <!-- Password strength indicator -->
          <div v-if="password" class="password-strength">
            <div class="strength-bar">
              <div 
                class="strength-fill"
                :style="{ 
                  width: `${(strengthResult.score / 5) * 100}%`,
                  backgroundColor: strengthResult.color
                }"
              ></div>
            </div>
            <div class="strength-info">
              <span 
                class="strength-label"
                :style="{ color: strengthResult.color }"
              >
                {{ $t(strengthResult.label) }}
              </span>
            </div>
            <ul v-if="strengthResult.feedbacks.length > 0" class="strength-feedback">
              <li v-for="item in strengthResult.feedbacks" :key="item">
                {{ $t(item) }}
              </li>
            </ul>
          </div>
        </div>
        
        <!-- Confirm password -->
        <div class="form-group">
          <label for="confirmPassword" class="label">{{ $t(tokens.register.confirmPassword) }} <span class="red">*</span></label>
          <input
            id="confirmPassword"
            type="password"
            v-model="formData.confirmPassword"
            @blur="validateField('confirmPassword')"
            :class="['input', { 'input-error': validationErrors.confirmPassword }]"
            :placeholder="$t(tokens.register.placeholder.confirmPassword)"
          />
          <div v-if="validationErrors.confirmPassword" class="field-error">
            {{ $t(validationErrors.confirmPassword) }}
          </div>
        </div>
        
        <!-- Submit button -->
        <button
          type="submit"
          :disabled="!isFormValid || isSubmitting"
          :class="['submit-button', { 
            'submit-button-disabled': !isFormValid || isSubmitting,
            'submit-button-loading': isSubmitting
          }]"
        >
          <svg v-if="isSubmitting" class="loading-spinner" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          {{ isSubmitting ? $t(tokens.register.submitting) : $t(tokens.register.submit) }}
        </button>
      </form>

      <!-- Login link -->
      <p class="login-link" v-if="submitSuccess">
        <router-link to="/login" class="link">{{ $t(tokens.login.title ) }}</router-link>
      </p>
    </div>
  </div>
</template>

<style scoped>
.registration-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 1rem;
}

.registration-card {
  background: white;
  border-radius: 16px;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  padding: 2rem;
  width: 100%;
  max-width: 420px;
}

.title {
  font-size: 2rem;
  font-weight: 700;
  text-align: center;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.subtitle {
  text-align: center;
  color: #6b7280;
  margin-bottom: 2rem;
}

.success-message {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background-color: #dcfce7;
  border: 1px solid #bbf7d0;
  color: #166534;
  padding: 0.75rem;
  border-radius: 8px;
  margin-bottom: 1rem;
  font-weight: 500;
}

.success-icon {
  width: 1.25rem;
  height: 1.25rem;
  flex-shrink: 0;
}

.error-message {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background-color: #fef2f2;
  border: 1px solid #fecaca;
  color: #dc2626;
  padding: 0.75rem;
  border-radius: 8px;
  margin-bottom: 1rem;
  font-weight: 500;
}

.error-icon {
  width: 1.25rem;
  height: 1.25rem;
  flex-shrink: 0;
}

.form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.label {
  font-weight: 600;
  color: #374151;
  font-size: 0.875rem;
}

.input {
  padding: 0.75rem;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.2s;
  background: white;
  color: black;
}

.input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.input-error {
  border-color: #ef4444;
}

.input-error:focus {
  border-color: #ef4444;
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

.field-error {
  color: #ef4444;
  font-size: 0.875rem;
  font-weight: 500;
}

.password-strength {
  margin-top: 0.5rem;
}

.strength-bar {
  height: 4px;
  background-color: #e5e7eb;
  border-radius: 2px;
  overflow: hidden;
  margin-bottom: 0.5rem;
}

.strength-fill {
  height: 100%;
  transition: all 0.3s ease;
  border-radius: 2px;
}

.strength-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.5rem;
}

.strength-label {
  font-size: 0.875rem;
  font-weight: 600;
}

.strength-feedback {
  list-style: none;
  padding: 0;
  margin: 0;
  font-size: 0.75rem;
  color: #6b7280;
}

.strength-feedback li {
  margin-bottom: 0.25rem;
}

.strength-feedback li::before {
  content: "• ";
  color: #ef4444;
  font-weight: bold;
}

.submit-button {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  padding: 0.875rem;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  margin-top: 1rem;
}

.submit-button:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 10px 25px -5px rgba(102, 126, 234, 0.4);
}

.submit-button-disabled {
  background: #9ca3af;
  cursor: not-allowed;
  transform: none !important;
  box-shadow: none !important;
}

.loading-spinner {
  width: 1.25rem;
  height: 1.25rem;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.login-link {
  text-align: center;
  margin-top: 1.5rem;
  color: #6b7280;
  font-size: 0.875rem;
}

.link {
  color: #667eea;
  text-decoration: none;
  font-weight: 600;
}

.link:hover {
  text-decoration: underline;
}

.red {
  color: #ef4444;
}

/* Responsive design */
@media (max-width: 480px) {
  .registration-card {
    padding: 1.5rem;
    margin: 0.5rem;
  }
  
  .title {
    font-size: 1.75rem;
  }
}
</style>
