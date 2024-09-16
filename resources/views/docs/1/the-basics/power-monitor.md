---
title: Power Monitor
order: 900
---

The power monitor allows you to gather information about the power state of the device.

## System Idle State

You can check if the system is idle with the `getSystemIdleState` method.

It expects a `int $threshold` argument, which is the number of seconds the system must be idle before it is considered idle. And it'll return an enum value of `SystemIdleStatesEnum`.


```php
use Native\Laravel\Enums\SystemIdleStatesEnum;
use Native\Laravel\Facades\PowerMonitor;

$state = PowerMonitor::getSystemIdleState(60);

if ($state === SystemIdleStatesEnum::IDLE) {
    // The system is idle!
}
```

The possible values for the `SystemIdleStatesEnum` enum are:

- `SystemIdleStatesEnum::ACTIVE`
- `SystemIdleStatesEnum::IDLE`
- `SystemIdleStatesEnum::LOCKED`
- `SystemIdleStatesEnum::UNKNOWN`

## System Idle Time

You can get the number of seconds the system has been idle with the `getSystemIdleTime` method.

```php
use Native\Laravel\Facades\PowerMonitor;

$seconds = PowerMonitor::getSystemIdleTime();
```

## Current Thermal State

You can get the current thermal state of the system with the `getCurrentThermalState` method. It'll return an enum value of `ThermalStatesEnum`.

```php
use Native\Laravel\Enums\ThermalStatesEnum;
use Native\Laravel\Facades\PowerMonitor;

$thermalState = PowerMonitor::getCurrentThermalState();

if ($state === ThermalStatesEnum::CRITICAL) {
    // Wow, the CPU is running hot!
}
```

The possible values for the `ThermalStatesEnum` enum are:

- `ThermalStatesEnum::UNKNOWN`
- `ThermalStatesEnum::NOMINAL`
- `ThermalStatesEnum::FAIR`
- `ThermalStatesEnum::SERIOUS`
- `ThermalStatesEnum::CRITICAL`

## Battery Information

You can determine if the device is running on battery power or AC power with the `isOnBatteryPower` method.

```php
use Native\Laravel\Facades\PowerMonitor;

if (PowerMonitor::isOnBatteryPower()) {
    // The device is running on battery power.
} else {
    // The device is running on AC power.
}
```

## Events

You can listen to the following events to get handle when the system's power state changes:

### PowerStateChanged

This `Native\Laravel\Events\PowerStateChanged` event is fired whenever the power state of the system changes. For example, when the system goes from battery power to AC power, or vice versa.

The event contains a public `$state` property which is an enum value of `Native\Laravel\Enums\PowerStatesEnum`.

### SpeedLimitChanged

This `Native\Laravel\Events\SpeedLimitChanged` event is fired whenever the CPU speed limit changes, usually due to thermal throttling or low battery.

The event contains a public `$limit` property which is the percentage of the maximum CPU speed that is currently allowed.

### ThermalStateChanged

The `Native\Laravel\Events\ThermalStateChanged` event is fired whenever the thermal state of the system changes.

The event contains a public `$state` property which is an enum value of `Native\Laravel\Enums\ThermalStatesEnum`.
