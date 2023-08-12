export const hasValue = (value) => value !== null && value !== undefined;

// export const callStatus = {
//   received: "received",
//   rejected: "rejected",
//   notCommunicated: "notCommunicated",
// };

export const callStatus = {
  success: "Call Completed",
  failure: "Call Failed",
};

export const batteryHealth = {
  na: "NA",
  good: "Good",
  moderate: "Moderate",
  cellDead: "Cell Dead",
};

export const loadStatus = {
  battery: "battery",
  dg: "dg",
  mains: "mains",
};

export function computeAlarms(flags, mainPower = 47, temperature = 43) {
  return {
    temperature: temperature > 43,
    bts: mainPower < 47,
    outage: mainPower < 46,
    door: flags.charAt(3) === "1",
    smoke: flags.charAt(4) === "1",
    theft: flags.charAt(6) === "1",
    pir: flags.charAt(2) === "1",
    currentDirection: flags.charAt(5) === "1",
    currentDirection1: flags.charAt(10) === "1",
    theft2: flags.charAt(8) !== "1",
    bbActive: flags.charAt(14) === "1",
    bb1Active: flags.charAt(15) === "1",
  };
}

export const getBatteryHealthStatus = (health) => {
  const value = Number(health);
  if (value === 0) return batteryHealth.na;
  if (value > 90) return batteryHealth.good;
  if (value >= 80 && value <= 90) return batteryHealth.moderate;
  if (value < 80) return batteryHealth.cellDead;
};

export const getLoadStatus = (secondaryPower2, secondaryPower3reserved) => {
  if (secondaryPower2 > 90) return loadStatus.mains;
  if (secondaryPower3reserved > 90) return loadStatus.dg;
  else return loadStatus.battery;
};

export const formatNumber = (value, decimals = 2) => {
  const num = Number(value);
  if (isNaN(num)) return value;

  return Number(num.toFixed(decimals));
  // .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
};
