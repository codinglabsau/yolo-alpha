# Provisioning

YOLO creates and manages all AWS resources required to run your application.

## Sync All

Provision everything at once:

```bash
yolo-alpha sync <environment>
```

This runs all sync commands in the correct order.

## Individual Sync Commands

You can also provision resources individually:

| Command | Description |
|---|---|
| `yolo-alpha sync:network <env>` | VPC, subnets, security groups, SSH keys |
| `yolo-alpha sync:standalone <env>` | Standalone app resources |
| `yolo-alpha sync:landlord <env>` | Landlord resources (multi-tenancy) |
| `yolo-alpha sync:tenant <env>` | Tenant resources (multi-tenancy) |
| `yolo-alpha sync:compute <env>` | EC2, autoscaling groups |
| `yolo-alpha sync:ci <env>` | CI/CD pipeline |
| `yolo-alpha sync:iam <env>` | IAM roles and policies |
| `yolo-alpha sync:logging <env>` | Logging and observability infrastructure |

## Dry Run

All sync commands support a `--dry-run` flag to preview changes without modifying anything on AWS:

```bash
yolo-alpha sync production --dry-run
```

This is a great way to see what resources will be created or modified before committing to any changes.
