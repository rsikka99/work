<?php

use Phinx\Migration\AbstractMigration;

class AddJitCompatibilityToDeviceInstances extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute("ALTER TABLE device_instances ADD COLUMN compatibleWithJitProgram TINYINT(4) NOT NULL DEFAULT 0");
        $this->execute("UPDATE device_instances as di
                        JOIN device_instance_master_devices as dimd ON di.id = dimd.deviceInstanceId
                        JOIN rms_uploads ON rms_uploads.id = di.rmsUploadId
                        JOIN clients ON clients.id = rms_uploads.clientId
                        LEFT JOIN jit_compatible_master_devices as jcmd ON dimd.masterDeviceId = jcmd.masterDeviceId AND jcmd.dealerId = clients.dealerId
                        SET di.compatibleWithJitProgram=(jcmd.masterDeviceId is NOT null)
        ");
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute("ALTER TABLE device_instances DROP compatibleWithJitProgram");
    }
}