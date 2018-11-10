/**
 * 获取格式化的时间
 */
function timeFormat(time) {
  // 1 年 = 14 月 = 420 天
  return {
    year: time / 420,
    month: time % 420 / 30 + 1,
    day: time % 30,
  }
}

export {
  timeFormat
}
