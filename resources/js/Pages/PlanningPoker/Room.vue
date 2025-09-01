<template>
  <Layout>
    <div class="min-h-[80vh] bg-gray-900">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- 房間資訊標題 -->
        <div class="bg-gray-800 border border-gray-700 p-6 mb-8">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <div class="w-14 h-14 bg-blue-600 flex items-center justify-center">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
              </div>
              <div>
                <h1 class="text-2xl font-bold text-white">參與者面板</h1>
                <p class="text-gray-400">歡迎, {{ userName }}</p>
              </div>
            </div>
            <div class="text-right">
              <div class="bg-gray-700 border border-gray-600 px-4 py-2 mb-2">
                <p class="text-gray-300 text-sm">房間代碼</p>
                <p class="font-bold text-white text-lg tracking-widest">{{ roomCode }}</p>
              </div>
              <div class="flex items-center justify-end">
                <div :class="[
                  'w-3 h-3 mr-2',
                  currentRoomData.status === 'voting' ? 'bg-yellow-500' : 'bg-green-500'
                ]"></div>
                <span class="text-gray-300 text-sm">
                  {{ currentRoomData.status === 'voting' ? '投票進行中' : '結果已揭曉' }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- 開牌結果總覽 -->
        <div v-if="currentRoomData.status === 'revealed'" class="bg-gray-800 border border-gray-700 p-8 mb-8">
          <h2 class="text-3xl font-bold text-white mb-8 text-center">投票結果揭曉</h2>
          
          <!-- 大卡片展示 -->
          <div class="grid gap-6 mb-8 justify-center" :class="{'grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6': true}">
            <div v-for="participant in currentParticipants.filter(p => currentVotes[p])" :key="participant" class="flex flex-col items-center space-y-3">
              <div class="w-24 h-32 bg-gradient-to-br from-white to-gray-100 border-4 border-blue-500 flex items-center justify-center shadow-2xl rounded-xl transform hover:scale-110 transition-all duration-300 hover:rotate-2"><span class="text-4xl font-bold text-gray-800">{{ currentVotes[participant] }}</span></div>
                <div class="text-center">
                  <p class="text-lg font-bold text-white">{{ participant }}</p>
                  <p class="text-sm text-blue-400 bg-blue-900/30 px-2 py-1 rounded-full">{{ currentVotes[participant] }} 點</p>
                </div>
            </div>
          </div>

          <!-- 沒投票的參與者 -->
          <div v-if="currentParticipants.filter(p => !currentVotes[p]).length > 0" class="mt-8">
            <h3 class="text-lg font-medium text-gray-400 mb-4 text-center">未投票</h3>
            <div class="flex flex-wrap justify-center gap-3">
              <div 
                v-for="participant in currentParticipants.filter(p => !currentVotes[p])" 
                :key="participant"
                class="px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg"
              >
                <span class="text-gray-300">{{ participant }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- 投票狀態統計 -->
        <div class="bg-gray-800 border border-gray-700 p-6 mb-8">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-white">
              {{ currentRoomData.status === 'voting' ? '投票進行中' : '投票結果' }}
            </h2>
            <div class="bg-gray-700 px-3 py-1 border border-gray-600">
              <span class="text-gray-300 text-sm">已投票: </span>
              <span class="text-white font-bold">{{ Object.keys(currentVotes).length }}/{{ currentParticipants.length }}</span>
            </div>
          </div>

          <!-- 參與者狀態網格 -->
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            <div 
              v-for="participant in currentParticipants" 
              :key="participant"
              class="bg-gray-700 border border-gray-600 p-4"
            >
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                  <div class="w-10 h-10 bg-gray-600 flex items-center justify-center text-white font-bold">
                    {{ participant.charAt(0).toUpperCase() }}
                  </div>
                  <div>
                    <p class="font-medium text-white">{{ participant }}</p>
                    <p class="text-xs text-gray-400">
                      {{ participant === userName ? '您' : (currentVotes[participant] ? '已投票' : '未投票') }}
                    </p>
                  </div>
                </div>
                <div class="flex items-center">
                  <!-- 已開牌且有投票 -->
                  <div 
                    v-if="currentRoomData.status === 'revealed' && currentVotes[participant]"
                    class="w-16 h-20 bg-white border-2 border-blue-500 flex items-center justify-center shadow-lg rounded-lg transform hover:scale-105 transition-all duration-300"
                  >
                    <span class="text-gray-800 font-bold text-xl">{{ currentVotes[participant] }}</span>
                  </div>
                  <!-- 投票中且已投票 -->
                  <div 
                    v-else-if="currentVotes[participant]"
                    class="w-12 h-16 bg-blue-600 flex items-center justify-center"
                  >
                    <div class="w-6 h-8 bg-white/80"></div>
                  </div>
                  <!-- 未投票 -->
                  <div 
                    v-else
                    class="w-12 h-16 bg-gray-600 flex items-center justify-center opacity-50"
                  >
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div v-if="currentParticipants.length === 0" class="text-center py-12">
            <div class="w-16 h-16 bg-gray-700 flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-1a.5.5 0 01.5.5v.5a.5.5 0 01-.5.5h-.5a.5.5 0 01-.5-.5v-.5a.5.5 0 01.5-.5h.5z"></path>
              </svg>
            </div>
            <p class="text-gray-400">等待其他成員加入房間...</p>
          </div>
          
          <!-- 非活躍參與者區域 -->
          <div v-if="currentInactiveParticipants.length > 0" class="mt-8">
            <h3 class="text-lg font-medium text-amber-400 mb-4 text-center flex items-center justify-center">
              <svg class="w-5 h-5 text-amber-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
              </svg>
              連線異常的參與者
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
              <div 
                v-for="participant in currentInactiveParticipants" 
                :key="`inactive-${participant}`"
                class="relative p-4 bg-gradient-to-br from-amber-50 to-amber-100 border-2 border-dashed border-amber-300 rounded-xl shadow-sm"
              >
                <!-- 離線狀態指示器 -->
                <div class="absolute top-2 right-2">
                  <div class="relative">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 1v6m0 10v6m11-7h-6M7 12H1"/>
                    </svg>
                    <div class="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
                  </div>
                </div>

                <div class="flex items-center justify-between">
                  <div class="flex items-center space-x-3">
                    <div class="relative">
                      <div class="w-10 h-10 bg-amber-500 flex items-center justify-center text-white font-bold rounded-lg shadow-md">
                        {{ participant.charAt(0).toUpperCase() }}
                      </div>
                      <!-- 離線圖標疊加 -->
                      <div class="absolute -bottom-1 -right-1 w-3 h-3 bg-gray-600 rounded-full flex items-center justify-center">
                        <svg class="w-2 h-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                      </div>
                    </div>
                    <div>
                      <p class="font-semibold text-amber-900">{{ participant }}</p>
                      <div class="flex items-center space-x-1 text-xs">
                        <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span class="text-amber-700 font-medium">
                          {{ participant === userName ? '您的連線中斷' : '連線中斷' }}
                        </span>
                      </div>
                    </div>
                  </div>
                  
                  <div class="flex items-center">
                    <!-- 顯示投票卡片（如果有） -->
                    <div 
                      v-if="currentRoomData.status === 'revealed' && currentVotes[participant]"
                      class="w-14 h-18 bg-white border-2 border-amber-400 flex items-center justify-center shadow-lg rounded-lg"
                    >
                      <span class="text-gray-800 font-bold text-lg">{{ currentVotes[participant] }}</span>
                    </div>
                    <!-- 投票狀態指示 -->
                    <div v-else-if="currentVotes[participant]" class="w-6 h-6 bg-amber-500 flex items-center justify-center rounded-lg shadow-sm">
                      <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                      </svg>
                    </div>
                    <!-- 未投票 -->
                    <div v-else class="w-6 h-6 bg-gray-300 flex items-center justify-center rounded-lg">
                      <svg class="w-3 h-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                  可能需要重新整理頁面來恢復連線，或檢查網路狀況
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- 卡牌選擇區域 -->
        <div v-if="currentRoomData.status === 'voting'" class="bg-gray-800 border border-gray-700 p-8">
          <div class="text-center mb-8">
            <h3 class="text-2xl font-bold text-white mb-2">選擇您的估點</h3>
            <p class="text-gray-400">點擊卡牌進行投票</p>
          </div>
          
          <!-- 卡牌網格 -->
          <div class="flex flex-wrap justify-center gap-4 mb-8 max-w-4xl mx-auto">
            <button
              v-for="card in cards"
              :key="card"
              @click="selectCard(card)"
              :class="[
                'w-16 h-24 border-2 font-bold text-xl transition-all duration-300 hover:border-blue-500 hover:scale-105',
                selectedCard === card
                  ? 'border-blue-500 bg-blue-600 text-white scale-105 shadow-lg'
                  : 'border-gray-600 bg-gray-700 text-gray-300 hover:bg-gray-600'
              ]"
            >
              {{ card }}
            </button>
          </div>
          
          <!-- 投票確認按鈕 -->
          <div class="text-center space-x-4">
            <button
              v-if="selectedCard && !hasVoted"
              @click="submitVote"
              :disabled="voting"
              class="bg-blue-600 text-white py-3 px-8 font-semibold hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center space-x-2"
            >
              <svg v-if="voting" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ voting ? '投票中...' : `確認投票: ${selectedCard}` }}</span>
            </button>
            
            <!-- 取消投票按鈕 -->
            <button
              v-if="hasVoted"
              @click="cancelVote"
              :disabled="voting"
              class="bg-red-600 text-white py-3 px-8 font-semibold hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center space-x-2"
            >
              <svg v-if="voting" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
              <span>{{ voting ? '取消中...' : `取消投票: ${currentVotes[userName]}` }}</span>
            </button>
          </div>
        </div>

        <!-- 等待或結果提示 -->
        <div v-if="currentRoomData.status === 'voting' && hasVoted" class="bg-gray-800 border border-gray-700 p-4 mt-6">
          <div class="flex items-center justify-center space-x-2 text-gray-400 text-sm">
            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>
            <span>您已投票完成，等待主持人開牌</span>
          </div>
        </div>

        <!-- 返回首頁 -->
        <div class="mt-8 text-center">
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
  userName: String,
  roomData: Object,
  votes: Object,
  participants: Array,
  inactiveParticipants: Array,
  cards: Array
})

const selectedCard = ref(null)
const voting = ref(false)
const currentVotes = ref({ ...props.votes })
const currentRoomData = ref({ ...props.roomData })
const currentParticipants = ref([...props.participants])
const currentInactiveParticipants = ref([...(props.inactiveParticipants || [])])

// 如果用戶已經投票，顯示其選擇
if (currentVotes.value[props.userName]) {
  selectedCard.value = currentVotes.value[props.userName]
}

// 檢查用戶是否已投票
const hasVoted = computed(() => {
  return currentVotes.value.hasOwnProperty(props.userName)
})

const selectCard = (card) => {
  if (currentRoomData.value.status === 'voting') {
    selectedCard.value = card
  }
}

const submitVote = async () => {
  if (!selectedCard.value || voting.value) return
  
  voting.value = true
  try {
    await axios.post(`/api/vote/${props.roomCode}`, {
      user_name: props.userName,
      vote: selectedCard.value
    })
    
    // 更新本地投票資料
    currentVotes.value[props.userName] = selectedCard.value
  } catch (error) {
    console.error('投票失敗:', error)
    alert('投票失敗，請重試')
  } finally {
    voting.value = false
  }
}

const cancelVote = async () => {
  if (voting.value) return
  
  voting.value = true
  try {
    // 發送取消投票請求 (刪除投票)
    await axios.delete(`/api/vote/${props.roomCode}/${props.userName}`)
    
    // 更新本地資料
    delete currentVotes.value[props.userName]
    selectedCard.value = null
  } catch (error) {
    console.error('取消投票失敗:', error)
    alert('取消投票失敗，請重試')
  } finally {
    voting.value = false
  }
}

// 用戶離開處理
const handleUserLeave = async () => {
  try {
    // 通知後端用戶離開
    await axios.post(`/api/leave/${props.roomCode}`, {
      user_name: props.userName
    })
  } catch (error) {
    console.error('離開房間失敗:', error)
  }
}

// 返回首頁處理
const goToHomePage = async () => {
  // 先處理用戶離開
  await handleUserLeave()
  
  // 跳轉到首頁
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
    // console.log('參與者頁面：同步投票狀態')
  }
  
  if (currentParticipantsStr !== newParticipantsStr) {
    currentParticipants.value = [...participants]
    hasUpdates = true
    // console.log('參與者頁面：同步參與者列表', {
    //   old: currentParticipants.value,
    //   new: participants,
    //   currentUser: props.userName
    // })
  }
  
  if (currentInactiveStr !== newInactiveStr) {
    currentInactiveParticipants.value = [...inactiveParticipants]
    hasUpdates = true
    // console.log('參與者頁面：同步非活躍參與者列表', {
    //   old: currentInactiveParticipants.value,
    //   new: inactiveParticipants
    // })
  }
  
  if (currentRoomData.value.status !== roomData.status) {
    currentRoomData.value = { ...roomData }
    hasUpdates = true
    // console.log('參與者頁面：同步房間狀態')
    
    // 如果狀態變為重置，清除選擇的卡牌
    if (roomData.status === 'voting') {
      selectedCard.value = currentVotes.value[props.userName] || null
    }
  }
  
  // 強制檢查是否需要更新參與者名單（防止自己被遺漏）
  if (!hasUpdates && participants && participants.length > 0) {
    const currentNames = [...currentParticipants.value]
    const newNames = [...participants]
    
    // 檢查當前用戶是否在參與者列表中
    const currentUserInList = currentNames.includes(props.userName)
    const currentUserInNewList = newNames.includes(props.userName)
    
    // 檢查是否有新用戶或遺失用戶
    const hasMissingUsers = newNames.some(name => !currentNames.includes(name))
    const hasExtraUsers = currentNames.some(name => !newNames.includes(name))
    
    if (hasMissingUsers || hasExtraUsers || (!currentUserInList && currentUserInNewList)) {
      // console.log('⚠參與者頁面：檢測到參與者名單不一致，強制同步', {
      //   currentUser: props.userName,
      //   inCurrentList: currentUserInList,
      //   inNewList: currentUserInNewList
      // })
      currentParticipants.value = [...participants]
      hasUpdates = true
    }
  }
  
  if (hasUpdates) {
    // console.log('參與者頁面：狀態已同步', {
    //   participants: participants.length,
    //   votes: Object.keys(votes).length,
    //   status: roomData.status,
    //   currentUserVisible: participants.includes(props.userName)
    // })
  }
}

onMounted(() => {
  // console.log('參與者頁面：連接 WebSocket 頻道', `room.${props.roomCode}`)
  
  // 連接到房間頻道
  channel = window.Echo.channel(`room.${props.roomCode}`)
  
  // 監聽連接狀態
  channel.subscribed(() => {
    // console.log('參與者成功訂閱頻道:', `room.${props.roomCode}`)
    
    // 連接成功後立即發送 "我還在房間" 訊號
    setTimeout(() => {
      axios.post('/api/still-here', {
        room_code: props.roomCode,
        user_name: props.userName,
        user_type: 'participant'
      }).catch(error => {
        console.warn('初始 still-here 失敗:', error)
      })
    }, 500) // 延遲500ms，確保連接穩定
    
    // 設置定期發送 "我還在房間" 訊號（每15秒）
    const stillHereInterval = setInterval(() => {
      axios.post('/api/still-here', {
        room_code: props.roomCode,
        user_name: props.userName,
        user_type: 'participant'
      }).catch(error => {
        console.warn('定期 still-here 失敗:', error)
      })
    }, 15000) // 每15秒發送一次
    
    // 保存 interval ID 以便清理
    window.roomStillHereInterval = stillHereInterval
  })
  
  channel.error((error) => {
    // console.error('參與者頻道錯誤:', error)
  })
  
  // 監聽投票更新
  channel.listen('.vote.updated', (e) => {
    // console.log('參與者收到投票更新：', e)
    // 更新所有投票資料
    currentVotes.value = { ...e.votes }
  })
  
  // 監聽參與者加入
  channel.listen('.participant.joined', (e) => {
    // console.log('參與者收到參與者加入：', e)
    // 更新參與者列表
    currentParticipants.value = [...e.participants]
  })
  
  // 監聽參與者離開
  channel.listen('.participant.left', (e) => {
    // console.log('參與者收到參與者離開：', e)
    // 更新參與者列表
    currentParticipants.value = [...e.participants]
    // 有人離開，移除其投票資料
    if (currentVotes.value[e.user_name]) {
      delete currentVotes.value[e.user_name]
      currentVotes.value = { ...currentVotes.value }
    }
  })
  
  // 監聽開牌事件
  channel.listen('.cards.revealed', () => {
    // console.log('參與者收到開牌事件')
    currentRoomData.value.status = 'revealed'
  })
  
  // 監聽重置事件
  channel.listen('.voting.reset', () => {
    // console.log('參與者收到重置事件')
    currentRoomData.value.status = 'voting'
    currentVotes.value = {}
    selectedCard.value = null
  })
  
  // 監聽房間狀態同步廣播
  channel.listen('.room.state.synced', (e) => {
    // console.log('參與者收到房間狀態同步廣播：', e)
    syncRoomState(e.roomData, e.votes, e.participants, e.inactive_participants || [])
  })
  
  // 監聽參與者變為 inactive 事件
  channel.listen('.participant.inactive', (e) => {
    console.log('參與者收到用戶變為 inactive：', e)
    currentParticipants.value = [...e.participants]
    currentInactiveParticipants.value = [...e.inactive_participants]
  })
  
  // 監聽參與者重新激活事件
  channel.listen('.participant.reactivated', (e) => {
    console.log('參與者收到用戶重新激活：', e)
    currentParticipants.value = [...e.participants]
    currentInactiveParticipants.value = [...e.inactive_participants]
  })
  
  // 用戶活動監聽器 - 任何用戶互動都發送 still-here 訊號
  const sendStillHereOnActivity = () => {
    axios.post('/api/still-here', {
      room_code: props.roomCode,
      user_name: props.userName,
      user_type: 'participant'
    }).catch(error => {
      console.warn('活動觸發 still-here 失敗:', error)
    })
  }
  
  // 監聽用戶互動事件
  document.addEventListener('click', sendStillHereOnActivity)
  document.addEventListener('keydown', sendStillHereOnActivity)
  document.addEventListener('scroll', sendStillHereOnActivity)
  
  // 監聽頁面關閉/離開事件
  const handleBeforeUnload = () => {
    handleUserLeave()
  }
  
  window.addEventListener('beforeunload', handleBeforeUnload)
  
  // 清理函數會在 unmount 時執行
  return () => {
    window.removeEventListener('beforeunload', handleBeforeUnload)
  }
})

// WebSocket 和頁面事件設置已合併到上面的 onMounted 中

onUnmounted(() => {
  handleUserLeave()
  
  // 清理 still-here interval
  if (window.roomStillHereInterval) {
    clearInterval(window.roomStillHereInterval)
    window.roomStillHereInterval = null
  }
  
  // 清理事件監聽器
  document.removeEventListener('click', sendStillHereOnActivity)
  document.removeEventListener('keydown', sendStillHereOnActivity)
  document.removeEventListener('scroll', sendStillHereOnActivity)
  
  if (channel) {
    window.Echo.leaveChannel(`room.${props.roomCode}`)
  }
})
</script>