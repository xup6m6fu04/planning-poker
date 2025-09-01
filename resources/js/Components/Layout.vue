<template>
  <div class="min-h-screen bg-gray-900 text-gray-100">
    <!-- Header -->
    <header class="bg-gray-800 border-b border-gray-700">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <!-- Logo -->
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 flex items-center justify-center">
              <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><title/><path d="M16,7,13,9V5.2071a.5.5,0,0,1,.8536-.3535Z" style="fill:#ff888b"/><path d="M18,7l3,2V5.2071a.5.5,0,0,0-.8536-.3535Z" style="fill:#ff888b"/><path d="M6,13H4V5A1,1,0,0,1,5,4H5A1,1,0,0,1,6,5Z" style="fill:#4b5661"/><path d="M5.5,22h0A1.5,1.5,0,0,1,4,20.5V13H7v7.5A1.5,1.5,0,0,1,5.5,22Z" style="fill:#4b5661"/><path d="M15.5,22h0A1.5,1.5,0,0,1,14,20.5V13h3v7.5A1.5,1.5,0,0,1,15.5,22Z" style="fill:#4b5661"/><path d="M17,19c-3-4-10-4-13,0V12c3-4,10-4,13,0Z" style="fill:#4b5661"/><path d="M17,14h0a4,4,0,0,1-4-4V7h8v3A4.00005,4.00005,0,0,1,17,14Z" style="fill:#4b5661"/><circle cx="15.25" cy="9.75" r="0.75" style="fill:#fff"/><circle cx="19" cy="9.75" r="0.75" style="fill:#fff"/></svg>
            </div>
            <div>
              <h1 class="text-xl font-bold text-white">Planning Poker</h1>
              <p class="text-xs text-gray-400">敏捷開發估點工具</p>
            </div>
          </div>

          <!-- Navigation -->
          <nav class="flex items-center space-x-6">
            <a href="/" class="text-gray-300 hover:text-white transition-colors">
              首頁
            </a>
            <a href="#" @click="showHelp" class="text-gray-300 hover:text-white transition-colors">
              說明
            </a>
          </nav>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 border-t border-gray-700 mt-auto">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <!-- 開發資訊 -->
          <div>
            <h3 class="text-lg font-semibold text-white mb-4">開發資訊</h3>
            <p class="text-gray-400 text-sm mb-2">開發者：Right</p>
            <p class="text-gray-400 text-sm mb-2">版本：v1.0.0</p>
            <p class="text-gray-400 text-sm">主要技術：Laravel + Inertia.js + Vue.js</p>
          </div>

          <!-- 聯絡資訊 -->
          <div>
            <h3 class="text-lg font-semibold text-white mb-4">技術支援</h3>
            <p class="text-gray-400 text-sm mb-2">歡迎共同協作</p>
            <div class="flex items-center space-x-4">
              <a href="https://github.com/xup6m6fu04/planning-poker" target="_blank" class="text-gray-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                  <path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61-.546-1.387-1.333-1.757-1.333-1.757-1.089-.744.083-.729.083-.729 1.205.084 1.84 1.236 1.84 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.418-1.305.762-1.605-2.665-.3-5.466-1.335-5.466-5.93 0-1.31.468-2.381 1.236-3.221-.124-.303-.536-1.523.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.655 1.653.243 2.873.12 3.176.77.84 1.235 1.911 1.235 3.221 0 4.61-2.803 5.625-5.475 5.921.43.372.823 1.102.823 2.222 0 1.606-.014 2.898-.014 3.293 0 .322.216.694.825.576C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12z"/>
                </svg>
              </a>
            </div>
          </div>
        </div>

        <div class="mt-6 pt-6 border-t border-gray-700 text-center">
          <p class="text-gray-400 text-sm">
            © 2025 Right Chou. All rights reserved.
          </p>
        </div>
      </div>
    </footer>

    <!-- 說明彈窗 -->
    <div v-if="showHelpModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-gray-800 border border-gray-700 p-8 max-w-2xl w-full mx-4 max-h-[80vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-2xl font-bold text-white">Planning Poker 使用說明</h3>
          <button
            @click="closeHelp"
            class="w-8 h-8 bg-gray-700 hover:bg-gray-600 flex items-center justify-center transition-colors"
          >
            <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <div class="space-y-6 text-gray-300">
          <!-- 使用步驟 -->
          <div>
            <h4 class="text-lg font-semibold text-white mb-3 flex items-center">
              <div class="w-6 h-6 bg-blue-600 flex items-center justify-center mr-2 text-sm font-bold">1</div>
              使用步驟
            </h4>
            <div class="space-y-3 ml-8">
              <div class="flex items-start space-x-3">
                <div class="w-6 h-6 bg-green-600 flex items-center justify-center text-xs font-bold mt-0.5">1</div>
                <div>
                  <p class="font-medium">創建房間</p>
                  <p class="text-sm text-gray-400">輸入主持人名稱，點擊「創建房間」</p>
                </div>
              </div>
              <div class="flex items-start space-x-3">
                <div class="w-6 h-6 bg-green-600 flex items-center justify-center text-xs font-bold mt-0.5">2</div>
                <div>
                  <p class="font-medium">邀請參與者</p>
                  <p class="text-sm text-gray-400">分享房間連結給團隊成員</p>
                </div>
              </div>
              <div class="flex items-start space-x-3">
                <div class="w-6 h-6 bg-green-600 flex items-center justify-center text-xs font-bold mt-0.5">3</div>
                <div>
                  <p class="font-medium">開始估點</p>
                  <p class="text-sm text-gray-400">參與者選擇卡牌進行投票</p>
                </div>
              </div>
              <div class="flex items-start space-x-3">
                <div class="w-6 h-6 bg-green-600 flex items-center justify-center text-xs font-bold mt-0.5">4</div>
                <div>
                  <p class="font-medium">查看結果</p>
                  <p class="text-sm text-gray-400">主持人點擊「開牌」查看所有人的投票</p>
                </div>
              </div>
              <div class="flex items-start space-x-3">
                <div class="w-6 h-6 bg-green-600 flex items-center justify-center text-xs font-bold mt-0.5">5</div>
                <div>
                  <p class="font-medium">重新投票</p>
                  <p class="text-sm text-gray-400">點擊「開始新一輪」重置投票</p>
                </div>
              </div>
            </div>
          </div>

          <!-- 功能特色 -->
          <div>
            <h4 class="text-lg font-semibold text-white mb-3 flex items-center">
              <div class="w-6 h-6 bg-purple-600 flex items-center justify-center mr-2 text-sm font-bold">✨</div>
              功能特色
            </h4>
            <div class="space-y-2 ml-8">
              <div class="flex items-center space-x-2">
                <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                <span>實時同步投票狀態</span>
              </div>
              <div class="flex items-center space-x-2">
                <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                <span>支援多人同時參與</span>
              </div>
              <div class="flex items-center space-x-2">
                <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                <span>簡潔直觀的操作介面</span>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-8 flex justify-end">
          <button
            @click="closeHelp"
            class="bg-blue-600 text-white px-6 py-2 font-semibold hover:bg-blue-700 transition-colors"
          >
            我知道了
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const showHelpModal = ref(false)

const showHelp = () => {
  showHelpModal.value = true
}

const closeHelp = () => {
  showHelpModal.value = false
}
</script>