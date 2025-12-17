<template>
  <div class="flex flex-col items-center">
    <!-- Gráfica SVG -->
    <svg :width="size" :height="size" class="transform -rotate-90">
      <circle
        v-for="(segment, index) in segments"
        :key="index"
        :cx="size / 2"
        :cy="size / 2"
        :r="radius"
        fill="transparent"
        :stroke="segment.color"
        :stroke-width="strokeWidth"
        :stroke-dasharray="`${segment.dashArray} ${circumference}`"
        :stroke-dashoffset="segment.offset"
        class="transition-all duration-500"
      />
      <!-- Círculo interior para efecto donut -->
      <circle
        :cx="size / 2"
        :cy="size / 2"
        :r="radius - strokeWidth / 2"
        fill="#000"
      />
    </svg>

    <!-- Leyenda -->
    <div class="mt-6 space-y-2 w-full">
      <div
        v-for="(item, index) in data"
        :key="index"
        class="flex items-center justify-between"
      >
        <div class="flex items-center gap-2">
          <div
            :style="{ backgroundColor: colors[index % colors.length] }"
            class="w-3 h-3 rounded-full"
          ></div>
          <span class="text-gray-400 text-sm">{{ item.label }}</span>
        </div>
        <span class="text-yellow-500 font-semibold text-sm">{{ item.value }}</span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface ChartData {
  label: string
  value: number
}

interface Props {
  data: ChartData[]
  size?: number
  strokeWidth?: number
  colors?: string[]
}

const props = withDefaults(defineProps<Props>(), {
  size: 200,
  strokeWidth: 30,
  colors: () => ['#c6a657', '#9b8447', '#7a6838', '#5a4c28']
})

const radius = computed(() => (props.size - props.strokeWidth) / 2)
const circumference = computed(() => 2 * Math.PI * radius.value)

const total = computed(() => 
  props.data.reduce((sum, item) => sum + item.value, 0)
)

const segments = computed(() => {
  let currentOffset = 0
  
  return props.data.map((item, index) => {
    const percentage = (item.value / total.value) * 100
    const dashArray = (percentage / 100) * circumference.value
    const offset = currentOffset
    
    currentOffset -= dashArray
    
    return {
      dashArray,
      offset,
      color: props.colors[index % props.colors.length]
    }
  })
})
</script>
