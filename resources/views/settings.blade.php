@extends('layouts.librenmsv1')

@section('title', 'Kafka Datastore Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Success/Error Messages -->
            @if (session('status'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <i class="fa fa-check-circle"></i> {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4><i class="fa fa-exclamation-triangle"></i> Validation Errors</h4>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fa fa-cogs"></i> Kafka Datastore Configuration
                        <span class="pull-right">
                            <a href="https://github.com/confluentinc/librdkafka/blob/master/CONFIGURATION.md" 
                               target="_blank" 
                               class="btn btn-xs btn-default" 
                               data-toggle="tooltip" 
                               data-placement="left" 
                               title="Click to view the complete Kafka configuration reference documentation">
                                <i class="fa fa-question-circle"></i> Configuration Reference
                            </a>
                        </span>
                    </h3>
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route('kafka-store.save-config') }}" class="form-horizontal">
                        @csrf
                        <!-- Enable/Disable Kafka -->
                        <div class="form-group">
                            <label for="kafka_enabled" class="col-sm-3 control-label">Enable Kafka Datastore</label>
                            <div class="col-sm-9">
                                <input type="checkbox" 
                                       id="kafka_enabled" 
                                       name="kafka_enabled" 
                                       value="{{ ($settings['kafka_enabled'] ?? false) ? 1 : '0' }}" 
                                       {{ ($settings['kafka_enabled'] ?? false) ? 'checked' : '' }}>
                                <span class="help-block">Enable sending metrics to Kafka</span>
                            </div>
                        </div>

                        <!-- Kafka Brokers -->
                        <div class="form-group">
                            <label for="kafka_brokers" class="col-sm-3 control-label">Kafka Brokers</label>
                            <div class="col-sm-9">
                                <input type="text" 
                                       class="form-control" 
                                       id="kafka_brokers" 
                                       name="kafka_brokers" 
                                       value="{{ $settings['kafka_brokers'] ?? 'localhost:9092' }}"
                                       placeholder="broker1:9092,broker2:9092">
                                <span class="help-block">Comma-separated list of Kafka broker addresses</span>
                            </div>
                        </div>

                        <!-- Device Field Exclusions -->
                        <div class="form-group">
                            <label for="kafka_device_fields" class="col-sm-3 control-label">Device Field Exclusions</label>
                            <div class="col-sm-9">
                                <input type="text" 
                                       class="form-control" 
                                       id="kafka_device_fields" 
                                       name="kafka_device_fields" 
                                       value="{{ $settings['kafka_device_fields'] ?? '' }}"
                                       placeholder="field1,field2">
                                <span class="help-block">Comma-separated list of device fields to exclude from Kafka</span>
                            </div>
                        </div>

                        <!-- Device Groups Exclusions -->
                        <div class="form-group">
                            <label for="kafka_device_groups" class="col-sm-3 control-label">Device Groups Exclusions</label>
                            <div class="col-sm-9">
                                <input type="text" 
                                       class="form-control" 
                                       id="kafka_device_groups" 
                                       name="kafka_device_groups" 
                                       value="{{ $settings['kafka_device_groups'] ?? '' }}"
                                       placeholder="group1,group2">
                                <span class="help-block">Comma-separated list of device groups to exclude from Kafka</span>
                            </div>
                        </div>

                        <!-- Device Measurement Exclusions -->
                        <div class="form-group">
                            <label for="kafka_device_measurements" class="col-sm-3 control-label">Device Measurement Exclusions</label>
                            <div class="col-sm-9">
                                <input type="text" 
                                       class="form-control" 
                                       id="kafka_device_measurements" 
                                       name="kafka_device_measurements" 
                                       value="{{ $settings['kafka_device_measurements'] ?? '' }}"
                                       placeholder="measurement1,measurement2">
                                <span class="help-block">Comma-separated list of device measurements to exclude from Kafka</span>
                            </div>
                        </div>

                        <!-- Kafka Topic -->
                        <div class="form-group">
                            <label for="kafka_topic" class="col-sm-3 control-label">Kafka Topic</label>
                            <div class="col-sm-9">
                                <input type="text" 
                                       class="form-control" 
                                       id="kafka_topic" 
                                       name="kafka_topic" 
                                       value="{{ $settings['kafka_topic'] ?? 'librenms-metrics' }}"
                                       placeholder="librenms-metrics">
                                <span class="help-block">Topic name for LibreNMS metrics</span>
                            </div>
                        </div>

                        <!-- Kafka Idempotence Enable/Disable -->
                        <div class="form-group">
                            <label for="kafka_idempotency" class="col-sm-3 control-label">Enable Idempotence</label>
                            <div class="col-sm-9">
                                <input type="checkbox" 
                                       id="kafka_idempotency" 
                                       name="kafka_idempotency" 
                                       value="1" 
                                       {{ ($settings['kafka_idempotency'] ?? false) ? 'checked' : '' }}>
                                <span class="help-block">Enable idempotence for Kafka producers</span>
                            </div>
                        </div>

                        <!-- SSL Configuration -->
                        <div class="form-group">
                            <label for="kafka_ssl_enabled" class="col-sm-3 control-label">Enable SSL/TLS</label>
                            <div class="col-sm-9">
                                <input type="checkbox" 
                                       id="kafka_ssl_enabled" 
                                       name="kafka_ssl_enabled" 
                                       value="1" 
                                       {{ ($settings['kafka_ssl_enabled'] ?? false) ? 'checked' : '' }}>
                                <span class="help-block">Enable SSL/TLS encryption for Kafka connection</span>
                            </div>
                        </div>

                        <!-- SSL Keystore Certificate Path -->
                        <div class="form-group ssl-config" style="{{ ($settings['kafka_ssl_enabled'] ?? false) ? '' : 'display: none;' }}">
                            <label for="kafka_ssl_keystore_location" class="col-sm-3 control-label">Kafka Client Keystore Location</label>
                            <div class="col-sm-9">
                                <input type="text" 
                                       class="form-control" 
                                       id="kafka_ssl_keystore_location" 
                                       name="kafka_ssl_keystore_location" 
                                       value="{{ $settings['kafka_ssl_keystore_location'] ?? '' }}"
                                       placeholder="/path/to/keystore.jks">
                                <span class="help-block">Path to Kafka client keystore file</span>
                            </div>
                        </div>

                        <!-- SSL Keystore Certificate Credential -->
                        <div class="form-group ssl-config" style="{{ ($settings['kafka_ssl_enabled'] ?? false) ? '' : 'display: none;' }}">
                            <label for="kafka_ssl_keystore_password" class="col-sm-3 control-label">Kafka Client Keystore Password</label>
                            <div class="col-sm-9">
                                <input type="password" 
                                       class="form-control" 
                                       id="kafka_ssl_keystore_password" 
                                       name="kafka_ssl_keystore_password" 
                                       value="{{ $settings['kafka_ssl_keystore_password'] ?? '' }}"
                                       placeholder="keystore-password">
                                <span class="help-block">Password for Kafka client keystore (optional)</span>
                            </div>
                        </div>

                        <!-- Batch Max Size -->
                        <div class="form-group">
                            <label for="kafka_batch_size" class="col-sm-3 control-label">Batch Max Size</label>
                            <div class="col-sm-9">
                                <input type="number" 
                                       class="form-control" 
                                       id="kafka_batch_size" 
                                       name="kafka_batch_size" 
                                       value="{{ $settings['kafka_batch_size'] ?? 25 }}"
                                       min="1" 
                                       max="10000">
                                <span class="help-block">Number of metrics to batch before sending to Kafka</span>
                            </div>
                        </div>

                        <!-- Buffer Max Size -->
                        <div class="form-group">
                            <label for="kafka_buffer_max_size" class="col-sm-3 control-label">Buffer Max Size</label>
                            <div class="col-sm-9">
                                <input type="number" 
                                       class="form-control" 
                                       id="kafka_buffer_max_size" 
                                       name="kafka_buffer_max_size" 
                                       value="{{ $settings['kafka_buffer_max_size'] ?? 100000 }}"
                                       min="1" 
                                       max="100000000">
                                <span class="help-block">Maximum size of the buffer for metrics before sending to Kafka</span>
                            </div>
                        </div>

                        <!-- Kafka Flush Timeout -->
                        <div class="form-group">
                            <label for="kafka_flush_timeout" class="col-sm-3 control-label">Kafka Flush Timeout</label>
                            <div class="col-sm-9">
                                <input type="number" 
                                       class="form-control" 
                                       id="kafka_flush_timeout" 
                                       name="kafka_flush_timeout" 
                                       value="{{ $settings['kafka_flush_timeout'] ?? 50 }}"
                                       min="1" 
                                       max="60000">
                                <span class="help-block">Maximum time to wait for flushing messages to Kafka (in milliseconds)</span>
                            </div>
                        </div>

                        <!-- Kafka Linger -->
                        <div class="form-group">
                            <label for="kafka_linger" class="col-sm-3 control-label">Kafka Linger</label>
                            <div class="col-sm-9">
                                <input type="number" 
                                       class="form-control" 
                                       id="kafka_linger" 
                                       name="kafka_linger" 
                                       value="{{ $settings['kafka_linger'] ?? 500 }}"
                                       min="0" 
                                       max="10000">
                                <span class="help-block">Maximum linger (in milliseconds)</span>
                            </div>
                        </div>

                        <!-- Kafka Required Acknowledgment -->
                        <div class="form-group">
                            <label for="kafka_required_ack" class="col-sm-3 control-label">Kafka Required Acknowledgment</label>
                            <div class="col-sm-9">
                                <input type="number" 
                                       class="form-control" 
                                       id="kafka_required_ack" 
                                       name="kafka_required_ack" 
                                       value="{{ $settings['kafka_required_ack'] ?? -1 }}"
                                       min="-1" 
                                       max="0"
                                       step="1">
                                <span class="help-block">0=Broker does not send any response/ack to client | -1 or all=Broker will block until message is committed by all in sync replicas (ISRs)</span>
                            </div>
                        </div>

                        <!-- Enable/Disable Kafka Debug Mode -->
                        <div class="form-group">
                            <label for="kafka_debug_enabled" class="col-sm-3 control-label">Enable Kafka Debug Mode</label>
                            <div class="col-sm-9">
                                <input type="checkbox" 
                                       id="kafka_debug_enabled" 
                                       name="kafka_debug_enabled" 
                                       value="1" 
                                       {{ ($settings['kafka_debug_enabled'] ?? false) ? 'checked' : '' }}>
                                <span class="help-block">Enable logging debug information from comunication with Kafka</span>
                            </div>
                        </div>

                        <!-- Kafka Security Comunication Debug -->
                        <div class="form-group">
                            <label for="kafka_security_debug" class="col-sm-3 control-label">Kafka Security Debug Flag</label>
                            <div class="col-sm-9">
                                <input type="text" 
                                       class="form-control" 
                                       id="kafka_security_debug" 
                                       name="kafka_security_debug" 
                                       value="{{ $settings['kafka_security_debug'] ?? '' }}"
                                       placeholder="generic, broker, topic, metadata, feature, queue, msg, protocol, cgrp, security, fetch, interceptor, plugin, consumer, admin, eos, mock, assignor, conf, telemetry, all">
                                <span class="help-block">Kafka Security Debug Flag</span>
                            </div>
                        </div>

                        <!-- Test Connection Button -->
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                                <button type="button" class="btn btn-info" id="test-connection">
                                    <i class="fa fa-plug"></i> Test Connection
                                </button>
                                <div id="test-result" class="alert" style="display: none; margin-top: 10px;"></div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Save Settings
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Toggle SSL configuration fields based on checkbox state
    $('#kafka_ssl_enabled').change(function() {
        if ($(this).is(':checked')) {
            $('.ssl-config').show();
        } else {
            $('.ssl-config').hide();
        }
    });
    
    // Test connection functionality
    $('#test-connection').click(function() {
        var button = $(this);
        var resultDiv = $('#test-result');
        
        button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Testing...');
        
        $.ajax({
            url: '',
            method: 'POST',
            data: $('form').serialize(),
            success: function(response) {
                if (response.success) {
                    resultDiv.removeClass('alert-danger').addClass('alert-success')
                           .html('<i class="fa fa-check"></i> Connection successful!')
                           .show();
                } else {
                    resultDiv.removeClass('alert-success').addClass('alert-danger')
                           .html('<i class="fa fa-times"></i> Connection failed: ' + response.message)
                           .show();
                }
            },
            error: function() {
                resultDiv.removeClass('alert-success').addClass('alert-danger')
                       .html('<i class="fa fa-times"></i> Connection test failed')
                       .show();
            },
            complete: function() {
                button.prop('disabled', false).html('<i class="fa fa-plug"></i> Test Connection');
            }
        });
    });
});
</script>
@endsection
