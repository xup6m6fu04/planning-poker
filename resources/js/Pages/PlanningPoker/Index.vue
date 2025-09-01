<template>
  <Layout>
    <div class="min-h-[80vh] bg-gray-900">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- 主標題區域 -->
        <div class="text-center mb-16">
          <h1 class="text-5xl font-bold text-white mb-6">Planning Poker</h1>
          <p class="text-xl text-gray-400 max-w-3xl mx-auto">
            敏捷開發團隊的專業估點工具，支援多人實時協作
          </p>
        </div>

        <!-- 錯誤訊息彈窗 -->
        <div v-if="errors.room_code || errors.error" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div class="bg-gray-800 border border-red-500 p-6 max-w-md w-full mx-4">
            <div class="flex items-center mb-4">
              <div class="w-12 h-12 bg-red-600 flex items-center justify-center mr-4">
                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
              </div>
              <div>
                <h3 class="text-lg font-semibold text-white mb-1">操作失敗</h3>
                <p class="text-red-300">{{ errors.room_code || errors.error }}</p>
              </div>
            </div>
            <div class="flex justify-end">
              <button
                @click="clearErrors"
                class="bg-red-600 text-white px-6 py-2 font-semibold hover:bg-red-700 transition-colors"
              >
                確認
              </button>
            </div>
          </div>
        </div>

        <!-- 主要操作區域 -->
        <div class="grid md:grid-cols-3 gap-8 mb-16">

          <!-- 創建房間 -->
          <div class="bg-gray-800 border border-gray-700 p-8">
            <div class="mb-8">
              <div class="w-16 h-16 bg-green-600 flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
              </div>
              <h2 class="text-2xl font-bold text-white mb-2">創建房間</h2>
              <p class="text-gray-400">作為主持人建立新的估點房間</p>
            </div>

            <div class="space-y-6">
              <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">主持人名稱</label>
                <div class="w-full px-4 py-3 bg-gray-700 border border-gray-600 text-white flex items-center justify-between">
                  <span>Host</span>
                  <div class="w-6 h-6 bg-green-600 flex items-center justify-center rounded-full">
                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                  </div>
                </div>
              </div>
              <button
                  @click="createRoom"
                  :disabled="createLoading"
                  class="w-full bg-green-600 text-white py-3 px-6 font-semibold hover:bg-green-700 transition-colors flex items-center justify-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <svg v-if="createLoading" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span>{{ createLoading ? '創建中...' : '創建房間' }}</span>
              </button>
            </div>
          </div>

          <!-- 加入房間 -->
          <div class="bg-gray-800 border border-gray-700 p-8">
            <div class="mb-8">
              <div class="w-16 h-16 bg-blue-600 flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
              </div>
              <h2 class="text-2xl font-bold text-white mb-2">加入房間</h2>
              <p class="text-gray-400">使用房間代碼加入現有的估點會議</p>
            </div>
            
            <form @submit.prevent="joinRoom" class="space-y-6">
              <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">房間代碼</label>
                <input
                  v-model="joinForm.room_code"
                  type="text"
                  placeholder="輸入房間代碼"
                  required
                  maxlength="6"
                  class="w-full px-4 py-3 bg-gray-700 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                />
              </div>
              <button
                type="submit"
                :disabled="!joinForm.room_code"
                class="w-full bg-blue-600 text-white py-3 px-6 font-semibold hover:bg-blue-700 transition-colors flex items-center justify-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                </svg>
                <span>加入房間</span>
              </button>
            </form>
          </div>

          <!-- 主持人重新連線 -->
          <div class="bg-gray-800 border border-gray-700 p-8">
            <div class="mb-8">
              <div class="w-16 h-16 bg-purple-600 flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <h2 class="text-2xl font-bold text-white mb-2">重新主持</h2>
              <p class="text-gray-400">斷線後重新連線至主持人面板</p>
            </div>
            
            <form @submit.prevent="rejoinAsHost" class="space-y-6">
              <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">房間代碼</label>
                <input
                  v-model="hostForm.room_code"
                  type="text"
                  placeholder="輸入房間代碼"
                  required
                  maxlength="6"
                  class="w-full px-4 py-3 bg-gray-700 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                />
              </div>
              <button
                type="submit"
                :disabled="hostLoading"
                class="w-full bg-purple-600 text-white py-3 px-6 font-semibold hover:bg-purple-700 transition-colors flex items-center justify-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <svg v-if="hostLoading" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 0 1 4 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ hostLoading ? '連線中...' : '重新主持' }}</span>
              </button>
            </form>
          </div>
        </div>

        <!-- 功能特色 -->
        <div class="grid md:grid-cols-4 gap-6">
          <div class="text-center p-6 bg-gray-800 border border-gray-700">
            <div class="w-12 h-12 bg-purple-600 flex items-center justify-center mx-auto mb-4">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
              </svg>
            </div>
            <h3 class="text-lg font-semibold text-white mb-2">即時同步</h3>
            <p class="text-gray-400 text-sm">WebSocket 實時更新資訊</p>
          </div>
          
          <div class="text-center p-6 bg-gray-800 border border-gray-700">
            <div class="w-12 h-12 bg-blue-600 flex items-center justify-center mx-auto mb-4">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
            </div>
            <h3 class="text-lg font-semibold text-white mb-2">多人協作</h3>
            <p class="text-gray-400 text-sm">支援無限參與者同時估點</p>
          </div>
          
          <div class="text-center p-6 bg-gray-800 border border-gray-700">
            <div class="w-12 h-12 bg-green-600 flex items-center justify-center mx-auto mb-4">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <h3 class="text-lg font-semibold text-white mb-2">標準卡牌</h3>
            <p class="text-gray-400 text-sm">Fibonacci 數列標準估點卡牌</p>
          </div>
          
          <div class="text-center p-6 bg-gray-800 border border-gray-700">
            <div class="w-12 h-12 bg-yellow-600 flex items-center justify-center mx-auto mb-4">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
            </div>
            <h3 class="text-lg font-semibold text-white mb-2">結果統計</h3>
            <p class="text-gray-400 text-sm">清晰的投票結果與狀態顯示</p>
          </div>
        </div>
      </div>
    </div>

    <!-- 選擇姓名彈窗 -->
    <div v-if="showNameModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-gray-800 border border-gray-700 p-8 max-w-md w-full mx-4">
        <h3 class="text-2xl font-bold text-white mb-6 text-center">選擇您的身份</h3>
        
        <!-- 預設人名選項 -->
        <div class="space-y-3 mb-6">
          <div 
            v-for="name in predefinedNames" 
            :key="name"
            @click="selectedName = name"
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
            @click="selectedName = 'custom'"
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
        <div v-if="selectedName === 'custom'" class="mb-6">
          <input
            v-model="customName"
            type="text"
            placeholder="請輸入您的姓名"
            class="w-full px-4 py-3 bg-gray-700 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            maxlength="20"
          />
        </div>

        <!-- 按鈕 -->
        <div class="flex space-x-4">
          <button
            @click="closeNameModal"
            class="flex-1 bg-gray-600 text-white py-3 px-6 font-semibold hover:bg-gray-700 transition-colors"
          >
            取消
          </button>
          <button
            @click="confirmJoinRoom"
            :disabled="!selectedName || (selectedName === 'custom' && !customName.trim()) || joinLoading"
            class="flex-1 bg-blue-600 text-white py-3 px-6 font-semibold hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center space-x-2"
          >
            <svg v-if="joinLoading" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>{{ joinLoading ? '加入中...' : '確認加入' }}</span>
          </button>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import Layout from '@/Components/Layout.vue'

const props = defineProps({
  errors: {
    type: Object,
    default: () => ({})
  },
  predefinedNames: {
    type: Array,
    default: () => []
  }
})

const joinLoading = ref(false)
const createLoading = ref(false)
const hostLoading = ref(false)
const showNameModal = ref(false)

const joinForm = ref({
  user_name: '',
  room_code: ''
})

const createForm = ref({
  host_name: 'Host'
})

const hostForm = ref({
  room_code: ''
})

const selectedName = ref('')
const customName = ref('')

const joinRoom = () => {
  if (!joinForm.value.room_code) return
  
  // 清空之前的選擇
  selectedName.value = ''
  customName.value = ''
  
  // 顯示選擇姓名的彈窗
  showNameModal.value = true
}

const confirmJoinRoom = () => {
  if (!selectedName.value && !customName.value.trim()) return
  
  joinLoading.value = true
  joinForm.value.user_name = selectedName.value === 'custom' ? customName.value.trim() : selectedName.value
  
  router.post('/join-room', joinForm.value, {
    onFinish: () => {
      joinLoading.value = false
      showNameModal.value = false
    }
  })
}

const closeNameModal = () => {
  showNameModal.value = false
  selectedName.value = ''
  customName.value = ''
}

const clearErrors = () => {
  // 清除錯誤訊息
  router.get(window.location.pathname, {}, { preserveState: false })
}

const createRoom = () => {
  createLoading.value = true
  router.post('/create-room', createForm.value, {
    onFinish: () => {
      createLoading.value = false
    }
  })
}

const rejoinAsHost = () => {
  hostLoading.value = true
  router.post('/join-host', hostForm.value, {
    onFinish: () => {
      hostLoading.value = false
    }
  })
}
</script>