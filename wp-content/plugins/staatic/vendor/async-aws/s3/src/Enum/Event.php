<?php

namespace Staatic\Vendor\AsyncAws\S3\Enum;

final class Event
{
    const S3_INTELLIGENT_TIERING = 's3:IntelligentTiering';
    const S3_LIFECYCLE_EXPIRATION_ALL = 's3:LifecycleExpiration:*';
    const S3_LIFECYCLE_EXPIRATION_DELETE = 's3:LifecycleExpiration:Delete';
    const S3_LIFECYCLE_EXPIRATION_DELETE_MARKER_CREATED = 's3:LifecycleExpiration:DeleteMarkerCreated';
    const S3_LIFECYCLE_TRANSITION = 's3:LifecycleTransition';
    const S3_OBJECT_ACL_PUT = 's3:ObjectAcl:Put';
    const S3_OBJECT_CREATED_ALL = 's3:ObjectCreated:*';
    const S3_OBJECT_CREATED_COMPLETE_MULTIPART_UPLOAD = 's3:ObjectCreated:CompleteMultipartUpload';
    const S3_OBJECT_CREATED_COPY = 's3:ObjectCreated:Copy';
    const S3_OBJECT_CREATED_POST = 's3:ObjectCreated:Post';
    const S3_OBJECT_CREATED_PUT = 's3:ObjectCreated:Put';
    const S3_OBJECT_REMOVED_ALL = 's3:ObjectRemoved:*';
    const S3_OBJECT_REMOVED_DELETE = 's3:ObjectRemoved:Delete';
    const S3_OBJECT_REMOVED_DELETE_MARKER_CREATED = 's3:ObjectRemoved:DeleteMarkerCreated';
    const S3_OBJECT_RESTORE_ALL = 's3:ObjectRestore:*';
    const S3_OBJECT_RESTORE_COMPLETED = 's3:ObjectRestore:Completed';
    const S3_OBJECT_RESTORE_DELETE = 's3:ObjectRestore:Delete';
    const S3_OBJECT_RESTORE_POST = 's3:ObjectRestore:Post';
    const S3_OBJECT_TAGGING_ALL = 's3:ObjectTagging:*';
    const S3_OBJECT_TAGGING_DELETE = 's3:ObjectTagging:Delete';
    const S3_OBJECT_TAGGING_PUT = 's3:ObjectTagging:Put';
    const S3_REDUCED_REDUNDANCY_LOST_OBJECT = 's3:ReducedRedundancyLostObject';
    const S3_REPLICATION_ALL = 's3:Replication:*';
    const S3_REPLICATION_OPERATION_FAILED_REPLICATION = 's3:Replication:OperationFailedReplication';
    const S3_REPLICATION_OPERATION_MISSED_THRESHOLD = 's3:Replication:OperationMissedThreshold';
    const S3_REPLICATION_OPERATION_NOT_TRACKED = 's3:Replication:OperationNotTracked';
    const S3_REPLICATION_OPERATION_REPLICATED_AFTER_THRESHOLD = 's3:Replication:OperationReplicatedAfterThreshold';
    public static function exists(string $value) : bool
    {
        return isset([self::S3_INTELLIGENT_TIERING => \true, self::S3_LIFECYCLE_EXPIRATION_ALL => \true, self::S3_LIFECYCLE_EXPIRATION_DELETE => \true, self::S3_LIFECYCLE_EXPIRATION_DELETE_MARKER_CREATED => \true, self::S3_LIFECYCLE_TRANSITION => \true, self::S3_OBJECT_ACL_PUT => \true, self::S3_OBJECT_CREATED_ALL => \true, self::S3_OBJECT_CREATED_COMPLETE_MULTIPART_UPLOAD => \true, self::S3_OBJECT_CREATED_COPY => \true, self::S3_OBJECT_CREATED_POST => \true, self::S3_OBJECT_CREATED_PUT => \true, self::S3_OBJECT_REMOVED_ALL => \true, self::S3_OBJECT_REMOVED_DELETE => \true, self::S3_OBJECT_REMOVED_DELETE_MARKER_CREATED => \true, self::S3_OBJECT_RESTORE_ALL => \true, self::S3_OBJECT_RESTORE_COMPLETED => \true, self::S3_OBJECT_RESTORE_DELETE => \true, self::S3_OBJECT_RESTORE_POST => \true, self::S3_OBJECT_TAGGING_ALL => \true, self::S3_OBJECT_TAGGING_DELETE => \true, self::S3_OBJECT_TAGGING_PUT => \true, self::S3_REDUCED_REDUNDANCY_LOST_OBJECT => \true, self::S3_REPLICATION_ALL => \true, self::S3_REPLICATION_OPERATION_FAILED_REPLICATION => \true, self::S3_REPLICATION_OPERATION_MISSED_THRESHOLD => \true, self::S3_REPLICATION_OPERATION_NOT_TRACKED => \true, self::S3_REPLICATION_OPERATION_REPLICATED_AFTER_THRESHOLD => \true][$value]);
    }
}
