<template>
  <Layout>
    <div class="min-h-[80vh] bg-gray-900">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- 主持人控制面板 -->
        <div class="bg-gray-800 border border-gray-700 p-8 mb-8">
          <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-4">
              <div class="w-16 h-16 bg-green-600 flex items-center justify-center">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div>
                <p class="text-gray-400 text-lg">主持人控制面板</p>
                <div class="bg-gray-700 border border-gray-600 px-3 py-1 mt-2 inline-block">
                  <span class="text-gray-300 text-sm">房間: </span>
                  <span class="font-bold text-white tracking-widest">{{ roomCode }}</span>
                </div>
              </div>
            </div>
            <div class="flex space-x-4">
              <button
                v-if="currentRoomData.status === 'voting'"
                @click="revealCards"
                :disabled="!canReveal || revealing"
                class="bg-blue-600 text-white py-3 px-8 font-semibold hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2"
              >
                <svg v-if="revealing" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                <span>{{ revealing ? '開牌中...' : '開牌' }}</span>
              </button>
              <button
                v-if="currentRoomData.status === 'revealed'"
                @click="resetVoting"
                :disabled="resetting"
                class="bg-green-600 text-white py-3 px-8 font-semibold hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2"
              >
                <svg v-if="resetting" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 0 1 4 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span>{{ resetting ? '重置中...' : '開始新一輪' }}</span>
              </button>
            </div>
          </div>

          <!-- 房間狀態指示器 -->
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-6">
              <div class="flex items-center space-x-2">
                <div :class="[
                  'w-4 h-4',
                  currentRoomData.status === 'voting' ? 'bg-yellow-500' : 'bg-green-500'
                ]"></div>
                <span class="text-white font-semibold">{{ currentRoomData.status === 'voting' ? '投票進行中' : '已開牌' }}</span>
              </div>
              <div class="bg-gray-700 border border-gray-600 px-4 py-2">
                <span class="text-gray-300 text-sm">投票進度: </span>
                <span class="text-white font-bold">{{ Object.keys(currentVotes).length }}/{{ currentParticipants.length }}</span>
                <span v-if="allVoted && currentParticipants.length > 0" class="ml-2 text-green-400">✓</span>
              </div>
              <div class="bg-gray-700 border border-gray-600 px-4 py-2">
                <span class="text-gray-300 text-sm">主持人: </span>
                <span class="text-white font-bold">{{ roomData.host }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- 開牌結果總覽 -->
        <div v-if="currentRoomData.status === 'revealed'" class="bg-gray-800 border border-gray-700 p-8 mb-8">
          <h2 class="text-2xl font-bold text-white mb-6 text-center">投票結果</h2>
          
          <!-- 投票卡片展示 -->
          <div class="flex flex-wrap justify-center gap-6 mb-8">
            <div 
              v-for="participant in currentParticipants.filter(p => currentVotes[p])" 
              :key="participant"
              class="flex flex-col items-center space-y-2"
            >
              <div class="w-20 h-28 bg-white border-3 border-blue-500 flex items-center justify-center shadow-xl transform hover:scale-110 transition-all duration-300 rounded-lg">
                <span class="text-3xl font-bold text-gray-800">{{ currentVotes[participant] }}</span>
              </div>
              <div class="text-center">
                <p class="text-sm font-medium text-white">{{ participant }}</p>
                <p class="text-xs text-blue-400">{{ currentVotes[participant] }} 點</p>
              </div>
            </div>
          </div>
        </div>

        <!-- 參與者狀態管理 -->
        <div class="mb-8">
          <div class="bg-gray-800 border border-gray-700 p-6">
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-xl font-semibold text-white">參與者狀態管理</h3>
              <div class="flex space-x-6 text-sm">
                <div class="flex items-center space-x-2">
                  <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                  <span class="text-gray-300">活躍: {{ currentParticipants.length }}</span>
                </div>
                <div class="flex items-center space-x-2">
                  <div class="w-3 h-3 bg-amber-500 rounded-full"></div>
                  <span class="text-gray-300">暫時斷線: {{ currentInactiveParticipants.length }}</span>
                </div>
                <div class="flex items-center space-x-2">
                  <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                  <span class="text-gray-300">已離線: {{ currentOfflineParticipants.length }}</span>
                </div>
                <div class="flex items-center space-x-2">
                  <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                  <span class="text-gray-300">已投票: {{ Object.keys(currentVotes).length }}</span>
                </div>
              </div>
            </div>

            <!-- 活躍參與者 -->
            <div v-if="currentParticipants.length > 0" class="mb-6">
              <h4 class="text-lg font-medium text-green-400 mb-4 flex items-center">
                <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                活躍參與者 ({{ currentParticipants.length }})
              </h4>
              <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                <div 
                  v-for="participant in currentParticipants" 
                  :key="participant"
                  class="flex items-center justify-between p-4 bg-gray-700 border border-gray-600 rounded-lg"
                >
                  <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-blue-600 flex items-center justify-center text-white font-bold text-lg rounded-lg">
                      {{ participant.charAt(0).toUpperCase() }}
                    </div>
                    <div class="flex-1">
                      <p class="font-medium text-white text-lg">{{ participant }}</p>
                      <p class="text-sm text-gray-400">
                        {{ currentVotes[participant] ? '已投票' : '等待投票' }}
                      </p>
                    </div>
                    <!-- 移除按鈕 -->
                    <button
                      @click="removeParticipant(participant)"
                      class="w-8 h-8 bg-red-600 hover:bg-red-700 flex items-center justify-center rounded-lg transition-colors"
                      title="移除參與者"
                    >
                      <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                      </svg>
                    </button>
                  </div>
                  
                  <!-- 投票結果顯示 -->
                  <div class="flex items-center">
                    <!-- 已開牌：顯示投票結果 -->
                    <div v-if="currentRoomData.status === 'revealed' && currentVotes[participant]" 
                         class="flex flex-col items-center">
                      <div class="w-20 h-24 bg-white border-3 border-blue-500 flex items-center justify-center shadow-xl rounded-lg transform hover:scale-105 transition-all duration-300">
                        <span class="text-3xl font-bold text-gray-800">{{ currentVotes[participant] }}</span>
                      </div>
                      <span class="text-xs text-blue-400 mt-1 font-medium">{{ currentVotes[participant] }} 點</span>
                    </div>
                    
                    <!-- 投票中：顯示狀態 -->
                    <div v-else class="flex items-center space-x-2">
                      <!-- 已投票 -->
                      <div 
                        v-if="currentVotes[participant]" 
                        class="w-8 h-8 bg-green-600 flex items-center justify-center rounded-lg"
                      >
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                      </div>
                      <!-- 未投票 -->
                      <div 
                        v-else 
                        class="w-8 h-8 bg-gray-600 flex items-center justify-center rounded-lg"
                      >
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- 非活躍參與者 -->
            <div v-if="currentInactiveParticipants.length > 0">
              <h4 class="text-lg font-medium text-amber-400 mb-4 flex items-center">
                <svg class="w-4 h-4 text-amber-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                連線異常的參與者 ({{ currentInactiveParticipants.length }})
              </h4>
              <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                <div 
                  v-for="participant in currentInactiveParticipants" 
                  :key="`inactive-${participant}`"
                  class="relative p-4 bg-gradient-to-br from-amber-50 to-amber-100 border-2 border-dashed border-amber-300 rounded-xl shadow-sm"
                >
                  <!-- 離線狀態指示器 -->
                  <div class="absolute top-2 right-2">
                    <div class="relative">
                      <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 1v6m0 10v6m11-7h-6M7 12H1"/>
                      </svg>
                      <div class="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
                    </div>
                  </div>

                  <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                      <div class="relative">
                        <div class="w-12 h-12 bg-amber-500 flex items-center justify-center text-white font-bold text-lg rounded-lg shadow-md">
                          {{ participant.charAt(0).toUpperCase() }}
                        </div>
                        <!-- 離線圖標疊加 -->
                        <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-gray-600 rounded-full flex items-center justify-center">
                          <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                          </svg>
                        </div>
                      </div>
                      <div>
                        <p class="font-semibold text-amber-900 text-lg">{{ participant }}</p>
                        <div class="flex items-center space-x-1 text-sm">
                          <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                          </svg>
                          <span class="text-amber-700 font-medium">連線中斷</span>
                        </div>
                      </div>
                    </div>
                    
                    <!-- 投票狀態顯示 -->
                    <div class="flex items-center">
                      <div 
                        v-if="currentRoomData.status === 'revealed' && currentVotes[participant]"
                        class="w-18 h-22 bg-white border-2 border-amber-400 flex items-center justify-center shadow-lg rounded-lg"
                      >
                        <span class="text-2xl font-bold text-gray-800">{{ currentVotes[participant] }}</span>
                      </div>
                      <!-- 投票狀態指示 -->
                      <div v-else-if="currentVotes[participant]" class="w-8 h-8 bg-amber-500 flex items-center justify-center rounded-lg shadow-sm">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                      </div>
                      <!-- 未投票 -->
                      <div v-else class="w-8 h-8 bg-gray-300 flex items-center justify-center rounded-lg">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                <div class="flex items-center space-x-2 text-amber-800">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  <p class="text-sm font-medium">
                    這些參與者可能需要重新整理頁面來恢復連線，或檢查網路狀況
                  </p>
                </div>
              </div>
            </div>

            <!-- 離線參與者 -->
            <div v-if="currentOfflineParticipants.length > 0">
              <h4 class="text-lg font-medium text-red-400 mb-4 mt-4 flex items-center">
                <svg class="w-4 h-4 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>
                </svg>
                已離線的參與者 ({{ currentOfflineParticipants.length }})
              </h4>
              <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                <div
                  v-for="participant in currentOfflineParticipants"
                  :key="`offline-${participant}`"
                  class="relative p-4 bg-gradient-to-br from-red-50 to-red-100 border-2 border-red-300 rounded-xl shadow-sm opacity-60"
                >
                  <!-- 離線狀態指示器 -->
                  <div class="absolute top-2 right-2">
                    <div class="relative">
                      <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636"/>
                      </svg>
                    </div>
                  </div>

                  <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                      <div class="relative">
                        <div class="w-12 h-12 bg-red-400 flex items-center justify-center text-white font-bold text-lg rounded-lg shadow-md opacity-75">
                          {{ participant.charAt(0).toUpperCase() }}
                        </div>
                        <!-- 離線圖標疊加 -->
                        <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-red-600 rounded-full flex items-center justify-center">
                          <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                          </svg>
                        </div>
                      </div>
                      <div>
                        <p class="font-semibold text-red-900 text-lg">{{ participant }}</p>
                        <div class="flex items-center space-x-1 text-sm">
                          <svg class="w-3 h-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636"/>
                          </svg>
                          <span class="text-red-700 font-medium">已離線</span>
                        </div>
                      </div>
                    </div>

                    <!-- 投票狀態顯示 -->
                    <div class="flex items-center">
                      <div
                        v-if="currentRoomData.status === 'revealed' && currentVotes[participant]"
                        class="w-18 h-22 bg-white border-2 border-red-400 flex items-center justify-center shadow-lg rounded-lg opacity-60"
                      >
                        <span class="text-2xl font-bold text-gray-600">{{ currentVotes[participant] }}</span>
                      </div>
                      <!-- 投票狀態指示 -->
                      <div v-else-if="currentVotes[participant]" class="w-8 h-8 bg-red-400 flex items-center justify-center rounded-lg shadow-sm opacity-75">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                      </div>
                      <!-- 未投票 -->
                      <div v-else class="w-8 h-8 bg-gray-400 flex items-center justify-center rounded-lg opacity-50">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-center space-x-2 text-red-800">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  <p class="text-sm font-medium">
                    這些參與者可能已經關閉瀏覽器或長時間無回應，需要重新加入房間
                  </p>
                </div>
              </div>
            </div>
            
            <div v-if="currentParticipants.length === 0 && currentInactiveParticipants.length === 0 && currentOfflineParticipants.length === 0" class="text-center py-12 text-gray-400">
              <div class="w-16 h-16 bg-gray-700 flex items-center justify-center mx-auto mb-4 rounded-lg">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path d="M18.5 19.5L20 21M11 14C7.13401 14 4 17.134 4 21H11M19 17.5C19 18.8807 17.8807 20 16.5 20C15.1193 20 14 18.8807 14 17.5C14 16.1193 15.1193 15 16.5 15C17.8807 15 19 16.1193 19 17.5ZM15 7C15 9.20914 13.2091 11 11 11C8.79086 11 7 9.20914 7 7C7 4.79086 8.79086 3 11 3C13.2091 3 15 4.79086 15 7Z" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <p class="text-lg">等待參與者加入房間...</p>
            </div>
          </div>
        </div>

        <!-- 分享連結 -->
        <div class="bg-gray-800 border border-gray-700 p-6 mb-8">
          <h3 class="text-lg font-semibold text-white mb-4">分享給參與者</h3>
          <div class="flex items-center space-x-3">
            <input 
              :value="participantUrl"
              readonly
              class="flex-1 px-3 py-2 bg-gray-700 border border-gray-600 text-white text-sm"
            />
            <button
              @click="copyUrl"
              class="px-4 py-2 bg-blue-600 text-white hover:bg-blue-700 transition-colors text-sm"
            >
              {{ copied ? '已複製' : '複製連結' }}
            </button>
          </div>
        </div>

        <!-- 返回首頁 -->
        <div class="text-center">
          <button 
            @click="goToHomePage" 
            class="text-gray-400 hover:text-white transition-colors"
          >
            ← 返回首頁
          </button>
        </div>

      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import axios from 'axios'
import Layout from '@/Components/Layout.vue'

const props = defineProps({
  roomCode: String,
  roomData: Object,
  votes: Object,
  participants: Array,
  inactiveParticipants: Array,
  offlineParticipants: Array,
  cards: Array
})

const currentRoomData = ref({ ...props.roomData })
const currentVotes = ref({ ...props.votes })
const currentParticipants = ref([...props.participants])
const currentInactiveParticipants = ref([...(props.inactiveParticipants || [])])
const currentOfflineParticipants = ref([...(props.offlineParticipants || [])])
const revealing = ref(false)
const resetting = ref(false)
const copied = ref(false)

const participantUrl = computed(() => {
  return `${window.location.origin}/room/${props.roomCode}`
})

// 檢查是否所有參與者都已投票
const allVoted = computed(() => {
  if (currentParticipants.value.length === 0) return false
  return currentParticipants.value.every(participant => 
    currentVotes.value.hasOwnProperty(participant)
  )
})

// 檢查是否可以開牌 (至少一人投票且所有參與者都投票)
const canReveal = computed(() => {
  return Object.keys(currentVotes.value).length > 0 && allVoted.value
})


const revealCards = async () => {
  revealing.value = true
  try {
    await axios.post(`/api/reveal/${props.roomCode}`)
    currentRoomData.value.status = 'revealed'
  } catch (error) {
    console.error('開牌失敗:', error)
    alert('開牌失敗，請重試')
  } finally {
    revealing.value = false
  }
}

const resetVoting = async () => {
  resetting.value = true
  try {
    await axios.post(`/api/reset/${props.roomCode}`)
    currentRoomData.value.status = 'voting'
    currentVotes.value = {}
  } catch (error) {
    console.error('重置失敗:', error)
    alert('重置失敗，請重試')
  } finally {
    resetting.value = false
  }
}

const copyUrl = async () => {
  try {
    await navigator.clipboard.writeText(participantUrl.value)
    copied.value = true
    setTimeout(() => {
      copied.value = false
    }, 2000)
  } catch (error) {
    console.error('複製失敗:', error)
  }
}

const removeParticipant = async (participantName) => {
  if (!confirm(`確定要移除參與者「${participantName}」嗎？`)) {
    return
  }

  try {
    await axios.post(`/api/remove-participant/${props.roomCode}`, {
      user_name: participantName
    })
    
    // 從本地狀態移除
    currentParticipants.value = currentParticipants.value.filter(p => p !== participantName)
    
    // 移除其投票
    if (currentVotes.value[participantName]) {
      delete currentVotes.value[participantName]
      currentVotes.value = { ...currentVotes.value }
    }
  } catch (error) {
    console.error('移除參與者失敗:', error)
    alert('移除參與者失敗，請重試')
  }
}

const goToHomePage = () => {
  // 主持人直接跳轉，房間會自動過期
  window.location.href = '/'
}

// WebSocket 連接
let channel = null

// 狀態同步處理函數
const syncRoomState = (roomData, votes, participants, inactiveParticipants = []) => {
  // 檢查狀態是否有變化，只在有變化時更新
  const currentVotesStr = JSON.stringify(currentVotes.value)
  const newVotesStr = JSON.stringify(votes)
  const currentParticipantsStr = JSON.stringify([...currentParticipants.value].sort())
  const newParticipantsStr = JSON.stringify([...participants].sort())
  const currentInactiveStr = JSON.stringify([...currentInactiveParticipants.value].sort())
  const newInactiveStr = JSON.stringify([...inactiveParticipants].sort())
  
  let hasUpdates = false
  
  if (currentVotesStr !== newVotesStr) {
    currentVotes.value = { ...votes }
    hasUpdates = true
    console.log('主持人頁面：同步投票狀態')
  }
  
  if (currentParticipantsStr !== newParticipantsStr) {
    currentParticipants.value = [...participants]
    hasUpdates = true
    console.log('主持人頁面：同步參與者列表', {
      old: currentParticipants.value, 
      new: participants 
    })
  }
  
  if (currentInactiveStr !== newInactiveStr) {
    currentInactiveParticipants.value = [...inactiveParticipants]
    hasUpdates = true
    console.log('主持人頁面：同步非活躍參與者列表', {
      old: currentInactiveParticipants.value, 
      new: inactiveParticipants
    })
  }
  
  if (currentRoomData.value.status !== roomData.status) {
    currentRoomData.value = { ...roomData }
    hasUpdates = true
    console.log('主持人頁面：同步房間狀態')
  }
  
  // 強制檢查是否需要更新參與者名單（防止遺漏）
  if (!hasUpdates && participants && participants.length > 0) {
    const currentNames = [...currentParticipants.value]
    const newNames = [...participants]
    
    // 檢查是否有新用戶或遺失用戶
    const hasMissingUsers = newNames.some(name => !currentNames.includes(name))
    const hasExtraUsers = currentNames.some(name => !newNames.includes(name))
    
    if (hasMissingUsers || hasExtraUsers) {
      console.log('主持人頁面：檢測到參與者名單不一致，強制同步')
      currentParticipants.value = [...participants]
      hasUpdates = true
    }
  }
  
  if (hasUpdates) {
    console.log('主持人頁面：狀態已同步', {
      participants: participants.length,
      votes: Object.keys(votes).length,
      status: roomData.status
    })
  }
}

onMounted(() => {
  // console.log('主持人頁面：連接 WebSocket 頻道', `room.${props.roomCode}`)
  // console.log('Echo 配置:', {
  //   broadcaster: window.Echo?.connector?.options?.broadcaster,
  //   key: window.Echo?.connector?.options?.key,
  //   wsHost: window.Echo?.connector?.options?.wsHost,
  //   wsPort: window.Echo?.connector?.options?.wsPort,
  // })
  
  // 連接到房間頻道
  channel = window.Echo.channel(`room.${props.roomCode}`)
  
  // 監聽連接狀態
  channel.subscribed(() => {
    // console.log('主持人成功訂閱頻道:', `room.${props.roomCode}`)
    
    // 主持人連接成功後立即發送 still-here 訊號
    setTimeout(() => {
      axios.post('/api/still-here', {
        room_code: props.roomCode,
        user_name: props.roomData.host,
        user_type: 'host'
      }).catch(error => {
        console.warn('主持人初始 still-here 失敗:', error)
      })
    }, 500)
    
    // 設置定期發送 "我還在房間" 訊號（每15秒）
    const hostStillHereInterval = setInterval(() => {
      axios.post('/api/still-here', {
        room_code: props.roomCode,
        user_name: props.roomData.host,
        user_type: 'host'
      }).catch(error => {
        console.warn('主持人定期 still-here 失敗:', error)
      })
    }, 15000)
    
    window.hostStillHereInterval = hostStillHereInterval
  })
  
  channel.error((error) => {
    // console.error('主持人頻道錯誤:', error)
  })
  
  // 監聽投票更新
  channel.listen('.vote.updated', (e) => {
    // console.log('主持人收到投票更新：', e)
    // 更新所有投票資料
    currentVotes.value = { ...e.votes }
    // 更新參與者列表
    currentParticipants.value = [...e.participants]
  })
  
  // 監聽參與者加入
  channel.listen('.participant.joined', (e) => {
    // console.log('主持人收到參與者加入：', e)
    currentParticipants.value = [...e.participants]
  })
  
  // 監聽參與者離開
  channel.listen('.participant.left', (e) => {
    // console.log('主持人收到參與者離開：', e)
    currentParticipants.value = [...e.participants]
    // 移除離開者的投票
    if (currentVotes.value[e.user_name]) {
      delete currentVotes.value[e.user_name]
      currentVotes.value = { ...currentVotes.value }
    }
  })
  
  // 監聽房間狀態同步廣播
  channel.listen('.room.state.synced', (e) => {
    // console.log('主持人收到房間狀態同步廣播：', e)
    syncRoomState(e.roomData, e.votes, e.participants, e.inactive_participants || [])
  })
  
  // 監聽參與者變為 inactive 事件
  channel.listen('.participant.inactive', (e) => {
    // console.log('主持人收到用戶變為 inactive：', e)
    currentParticipants.value = [...e.participants]
    currentInactiveParticipants.value = [...e.inactive_participants]
  })
  
  // 監聽參與者重新激活事件
  channel.listen('.participant.reactivated', (e) => {
    // console.log('主持人收到用戶重新激活：', e)
    currentParticipants.value = [...e.participants]
    currentInactiveParticipants.value = [...e.inactive_participants]
  })
  
  // 主持人用戶活動監聽器
  const sendHostStillHereOnActivity = () => {
    axios.post('/api/still-here', {
      room_code: props.roomCode,
      user_name: props.roomData.host,
      user_type: 'host'
    }).catch(error => {
      console.warn('主持人活動觸發 still-here 失敗:', error)
    })
  }
  
  // 監聽主持人互動事件
  document.addEventListener('click', sendHostStillHereOnActivity)
  document.addEventListener('keydown', sendHostStillHereOnActivity)
})

onUnmounted(() => {
  // 清理主持人 still-here interval
  if (window.hostStillHereInterval) {
    clearInterval(window.hostStillHereInterval)
    window.hostStillHereInterval = null
  }
  
  // 清理事件監聽器
  document.removeEventListener('click', sendHostStillHereOnActivity)
  document.removeEventListener('keydown', sendHostStillHereOnActivity)
  
  if (channel) {
    window.Echo.leaveChannel(`room.${props.roomCode}`)
  }
})
</script>