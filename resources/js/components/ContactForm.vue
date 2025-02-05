<template>
  <form @submit.prevent="submitForm" enctype="multipart/form-data">
    <!-- Name Field -->
    <div>
      <label>Name:</label>
      <input v-model="formData.name" @blur="validateName" 
             pattern=".{2,10}" title="2-10 characters">
      <span class="error">{{ errors.name }}</span>
    </div>

    <!-- Email Field -->
    <div>
      <label>Email:</label>
      <input v-model="formData.email" type="email" @blur="validateEmail">
      <span class="error">{{ errors.email }}</span>
    </div>

    <!-- Phone Field -->
    <div>
      <label>Phone:</label>
      <input v-model="formData.phone" @blur="validatePhone" 
             pattern="\+\d+" title="Include country code">
      <span class="error">{{ errors.phone }}</span>
    </div>

    <!-- Address Fields -->
    <div>
      <label>Street:</label>
      <input v-model="formData.street">
    </div>
    <div>
      <label>State:</label>
      <input v-model="formData.state">
    </div>
    <div>
      <label>Zip:</label>
      <input v-model="formData.zip">
    </div>
    <div>
      <label>Country:</label>
      <input v-model="formData.country">
    </div>

    <!-- Message Field -->
    <div>
      <label>Message:</label>
      <textarea v-model="formData.message" @blur="validateMessage"></textarea>
      <span class="error">{{ errors.message }}</span>
    </div>

    <!-- File Uploads -->
    <div>
      <label>Images (JPG only):</label>
      <input type="file" accept="image/jpeg" @change="handleImageUpload" multiple>
      <span class="error">{{ errors.images }}</span>
    </div>

    <div>
      <label>Files (PDF only):</label>
      <input type="file" accept="application/pdf" @change="handleFileUpload" multiple>
      <span class="error">{{ errors.files }}</span>
    </div>

    <button type="submit" :disabled="submitting">
      {{ submitting ? 'Submitting...' : 'Submit' }}
    </button>
    
    <div v-if="successMessage" class="success">{{ successMessage }}</div>
    <div v-if="errorMessage" class="error">{{ errorMessage }}</div>
  </form>
</template>

<script>
export default {
  data() {
    return {
      formData: {
        name: '',
        email: '',
        phone: '',
        message: '',
        street: '',
        state: '',
        zip: '',
        country: '',
        images: [],
        files: []
      },
      errors: {},
      submitting: false,
      successMessage: '',
      errorMessage: ''
    };
  },
  methods: {
    validateName() {
      if (this.formData.name.length < 2 || this.formData.name.length > 10) {
        this.errors.name = 'Name must be between 2-10 characters';
      } else {
        delete this.errors.name;
      }
    },
    validateEmail() {
      const emailRegex = /^[^\s@]+@([^\s@]+\.)+[^\s@]+$/;
      if (!emailRegex.test(this.formData.email)) {
        this.errors.email = 'Invalid email format';
      } else if (this.formData.email.endsWith('@gmail.com')) {
        this.errors.email = 'Gmail addresses are not allowed';
      } else {
        delete this.errors.email;
      }
    },
    validatePhone() {
      const phoneRegex = /^\+\d+$/;
      if (!phoneRegex.test(this.formData.phone)) {
        this.errors.phone = 'Invalid phone format (include country code)';
      } else {
        delete this.errors.phone;
      }
    },
    validateMessage() {
      if (this.formData.message.length < 10) {
        this.errors.message = 'Message must be at least 10 characters';
      } else {
        delete this.errors.message;
      }
    },
    handleImageUpload(e) {
      const files = Array.from(e.target.files);
      const invalid = files.some(file => file.type !== 'image/jpeg');
      if (invalid) {
        this.errors.images = 'Only JPG images allowed';
      } else {
        this.formData.images = files;
        delete this.errors.images;
      }
    },
    handleFileUpload(e) {
      const files = Array.from(e.target.files);
      const invalid = files.some(file => file.type !== 'application/pdf');
      if (invalid) {
        this.errors.files = 'Only PDF files allowed';
      } else {
        this.formData.files = files;
        delete this.errors.files;
      }
    },
    async submitForm() {
      this.validateName();
      this.validateEmail();
      this.validatePhone();
      this.validateMessage();

      if (Object.keys(this.errors).length > 0) return;

      this.submitting = true;
      this.errorMessage = '';
      this.successMessage = '';

      const formData = new FormData();
      Object.entries(this.formData).forEach(([key, value]) => {
        if (key === 'images' || key === 'files') {
          value.forEach(file => formData.append(`${key}[]`, file));
        } else {
          formData.append(key, value);
        }
      });

      try {
        const response = await axios.post('/submissions', formData, {
          headers: {'Content-Type': 'multipart/form-data'}
        });
        
        if (response.data.success) {
          this.successMessage = 'Submission successful!';
          this.formData = { /* reset form data */ };
        }
      } catch (error) {
        this.errorMessage = 'Submission failed. Please try again.';
      } finally {
        this.submitting = false;
      }
    }
  }
};
</script>

<style>
.error { color: red; }
.success { color: green; }
</style>