<template>
  <Layout>
    <div class="min-h-[70vh] bg-gray-900 py-12 flex items-center justify-center">
      <div class="max-w-md w-full mx-4">
        <!-- 主卡片 -->
        <div class="bg-gray-800 border border-gray-700 p-8">
          <!-- 標題區域 -->
          <div class="text-center mb-8">
            <div class="w-16 h-16 bg-blue-600 flex items-center justify-center mb-6 mx-auto">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
            </div>
            <h1 class="text-3xl font-bold text-white mb-4">
              加入房間
            </h1>
            <div class="bg-gray-700 border border-gray-600 p-4 mb-6">
              <p class="text-gray-300 text-sm mb-2">房間代碼</p>
              <p class="font-bold text-white text-2xl tracking-widest">{{ roomCode }}</p>
            </div>
          </div>

          <!-- 選擇身份 -->
          <div class="space-y-6">
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-4">請選擇您的身份</label>
              
              <!-- 預設人名選項 -->
              <div class="space-y-3 mb-4">
                <div 
                  v-for="name in predefinedNames" 
                  :key="name"
                  @click="selectName(name)"
                  :class="[
                    'p-3 border cursor-pointer transition-colors',
                    selectedName === name 
                      ? 'border-blue-500 bg-blue-600 text-white' 
                      : 'border-gray-600 bg-gray-700 text-gray-300 hover:bg-gray-600'
                  ]"
                >
                  {{ name }}
                </div>
                
                <!-- 自定義選項 -->
                <div 
                  @click="selectName('custom')"
                  :class="[
                    'p-3 border cursor-pointer transition-colors',
                    selectedName === 'custom' 
                      ? 'border-blue-500 bg-blue-600 text-white' 
                      : 'border-gray-600 bg-gray-700 text-gray-300 hover:bg-gray-600'
                  ]"
                >
                  自定義姓名
                </div>
              </div>

              <!-- 自定義名稱輸入框 -->
              <div v-if="selectedName === 'custom'" class="mb-4">
                <input
                  v-model="customName"
                  type="text"
                  placeholder="請輸入您的姓名"
                  class="w-full px-4 py-3 bg-gray-700 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  maxlength="20"
                />
              </div>
            </div>

            <button 
              @click="joinRoom"
              :disabled="loading || !selectedName || (selectedName === 'custom' && !customName.trim())"
              class="w-full bg-blue-600 text-white py-3 px-6 font-semibold hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center space-x-2"
            >
              <svg v-if="loading" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
              </svg>
              <span>{{ loading ? '加入中...' : '進入房間' }}</span>
            </button>
          </div>

          <!-- 返回首頁 -->
          <div class="mt-6 text-center">
            <a href="/" class="text-gray-400 hover:text-white transition-colors">← 返回首頁</a>
          </div>
        </div>

        <!-- 提示說明 -->
        <div class="bg-gray-800 border border-gray-700 p-4 mt-6">
          <p class="text-center text-gray-400 text-sm">
            💡 進入房間後，您可以選擇故事點數進行投票
          </p>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref } from 'vue'
import Layout from '@/Components/Layout.vue'

const props = defineProps({
  roomCode: String,
  predefinedNames: {
    type: Array,
    default: () => []
  }
})

const userName = ref('')
const loading = ref(false)
const selectedName = ref('')
const customName = ref('')
const showNameSelection = ref(true)

const selectName = (name) => {
  selectedName.value = name
  if (name !== 'custom') {
    userName.value = name
  }
}

const joinRoom = () => {
  const finalName = selectedName.value === 'custom' ? customName.value.trim() : selectedName.value
  if (!finalName) return
  
  loading.value = true
  // 使用現有 URL 加上 name 參數
  window.location.href = `/room/${props.roomCode}?name=${encodeURIComponent(finalName)}`
}
</script>