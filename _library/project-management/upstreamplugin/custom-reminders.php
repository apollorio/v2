<?php
/**
 * Plugin Name: Custom UpStream Reminders
 * Plugin URI:
 * Description: Allow to customize the UpStream reminders
 * Author:
 * Author URI:
 * Version: 0.1.0
 *
 * Plugin bootstrap file.
 */

// Prevent direct access.
if (!defined('ABSPATH')) exit;

use \UpStream\Plugins\EmailNotifications\Plugin;

/**
 * Removes the current action and replaces with a new function where you can
 * customize the output.
 */
function custom_upstream_reminder() {
	remove_all_actions( 'upstream.email-notifications:checkUpcomingReminders' );
	add_action( 'upstream.email-notifications:checkUpcomingReminders', 'custom_upstream_reminder_check_reminders' );
}

// Inialize our plugin.
add_action( 'init', 'custom_upstream_reminder', 1 );


/**
 * Checks for upcoming reminders and send out notifications as needed.
 *
 * The original method wasn't designed to be overriden, so please, be careful to
 * customize only the output markup, usually on the lines containing:
 *
 * $html, $html[], etc...
 *
 */
function custom_upstream_reminder_check_reminders()
{
    // Cache all projects.
    $projects = call_user_func(function() {
        $projects = array();

        $rowset = get_posts(array(
            'post_type'   => "project",
            'post_status' => "publish"
        ));

        if (count($rowset) > 0) {
            $siteURL = get_site_url('', 'project');

            foreach ($rowset as $row) {
                $meta = (array)get_post_meta($row->ID, '_upstream_project_disable_all_notifications');
                if (count($meta) > 0 && $meta[0] === 'on') {
                    continue;
                }

                $projects[$row->ID] = (object)array(
                    'id'         => $row->ID,
                    'title'      => $row->post_title,
                    'url'        => $siteURL .'/'. $row->post_name,
                    'milestones' => array(),
                    'tasks'      => array(),
                    'bugs'       => array()
                );
            }

            if (count($projects) > 0) {
                global $wpdb;

                $rowset = $wpdb->get_results(sprintf('
                    SELECT `post_id`, `meta_key`, `meta_value`
                    FROM `%s`
                    WHERE `post_id` IN (%s)
                      AND `meta_key` IN ("_upstream_project_milestones", "_upstream_project_tasks", "_upstream_project_bugs")',
                    $wpdb->prefix . 'postmeta',
                    implode(', ', array_keys($projects))
                ));

                foreach ($rowset as $row) {
                    $itemsList = maybe_unserialize($row->meta_value);
                    if (!empty($itemsList)) {
                        $itemType = str_replace('_upstream_project_', '', $row->meta_key);

                        if (isset($itemsList[0]) && is_array($itemsList[0]) && isset($itemsList[0][0])) {
                            $itemsList = $itemsList[0];
                        }

                        $projects[(int)$row->post_id]->{$itemType} = array();
                        foreach ($itemsList as $item) {
                            $item = (array)$item;
                            $projects[(int)$row->post_id]->{$itemType}[$item['id']] = $item;
                        }
                    }
                }
            }
        }

        return $projects;
    });

    if (count($projects) === 0) {
        return;
    }

    // Cache all users that might be notified.
    $users = call_user_func(function() {
        $users = array();

        $rowset = get_users(array(
            'role__in' => array('administrator', 'upstream_manager', 'upstream_user')
        ));

        foreach ($rowset as $user) {
            $users[$user->ID] = (object)array(
                'name'      => $user->display_name,
                'email'     => $user->user_email,
                'reminders' => array()
            );
        }

        return $users;
    });

    $adminEmail = get_bloginfo('admin_email');
    $currentTimestamp = time();
    $secondsInADay = 1 * 60 * 60 * 24;
    $itemsType = array('milestones', 'tasks', 'bugs');
    $emailSubject = sprintf(_x('%s Notifications About Upcoming Due Dates', '%s: Site name', 'upstream-email-notifications'), get_bloginfo('name'));

    $itemsIdsCache = array();
    $projectsCache = array();
    $milestones    = getMilestonesTitles();

    $itemsLabelsMap = array(
        'milestones' => upstream_milestone_label_plural(),
        'tasks'      => upstream_task_label_plural(),
        'bugs'       => upstream_bug_label_plural()
    );

    foreach ($projects as &$project) {
        $projectsCache[$project->id] = $project;

        foreach ($itemsType as $itemType) {
            if (! isset( $project->{$itemType}) || count($project->{$itemType}) === 0) {
                continue;
            }

            foreach ($project->{$itemType} as $item) {
                $item = (object)$item;

                if (
                    // Check if the item has an end/due date.
                    (empty($item->due_date) && empty($item->end_date))
                    ||
                    // Check if the assigned user is valid.
                    (
                        !isset($item->assigned_to)
                        || empty($item->assigned_to)
                    )
                    ||
                    // Check if there's any reminder set.
                    (!isset($item->reminders) || empty($item->reminders))
                ) {
                    continue;
                }

                $endDate = (int) (isset($item->due_date) ? $item->due_date : $item->end_date);

                $assignees = array();
                $item->assigned_to = is_array($item->assigned_to) ? $item->assigned_to : array($item->assigned_to);
                $item->assigned_to = array_filter(array_map('intval', $item->assigned_to));

                foreach ($item->assigned_to as $assignee) {
                    if (isset($users[$assignee])) {
                        $assignees[$assignee] = &$users[$assignee];
                    }
                }

                $reminders = array_filter(array_map('json_decode', (array)$item->reminders));

                foreach ($reminders as $reminder) {
                    // Check if the notification was already sent.
                    if ((bool)$reminder->sent) {
                        continue;
                    }

                    switch ((int)$reminder->reminder) {
                        case 1:
                            $daysInterval = 1;
                            break;
                        case 2:
                            $daysInterval = 2;
                            break;
                        case 3:
                            $daysInterval = 3;
                            break;
                        case 4:
                            $daysInterval = 7;
                            break;
                        case 5:
                            $daysInterval = 14;
                            break;
                        default:
                            continue;
                    }

                    // Check if the notification is past due already.
                    if ($currentTimestamp >= $endDate) {
                        foreach ($assignees as $assigneeId => $assignee) {
                            if (!isset($assignee->reminders[$project->id])) {
                                $assignee->reminders[$project->id] = array(
                                    'milestones' => array(),
                                    'tasks'      => array(),
                                    'bugs'       => array(),
                                    'past_due'   => array(
                                        'milestones' => array(),
                                        'tasks'      => array(),
                                        'bugs'       => array(),
                                        'count'      => 0
                                    )
                                );

                                if (!isset($itemsIdsCache[$item->id])) {
                                    array_push($assignee->reminders[$project->id]['past_due'][$itemType], $item);
                                    $itemsIdsCache[$item->id] = 1;
                                    $assignee->reminders[$project->id]['past_due']['count']++;
                                }

                                $assignees[$assigneeId] = $assignee;
                            }
                        }
                    } else if ($currentTimestamp + ($secondsInADay * $daysInterval) >= $endDate) {
                        foreach ($assignees as $assigneeId => $assignee) {
                            if (!isset($assignee->reminders[$project->id])) {
                                $assignee->reminders[$project->id] = array(
                                    'milestones' => array(),
                                    'tasks'      => array(),
                                    'bugs'       => array(),
                                    'files'      => array(),
                                    'past_due'   => array(
                                        'milestones' => array(),
                                        'tasks'      => array(),
                                        'bugs'       => array(),
                                        'count'      => 0
                                    )
                                );

                            }

                            if (!isset($itemsIdsCache[$item->id])) {
                                array_push($assignee->reminders[$project->id][$itemType], $item);
                                $itemsIdsCache[$item->id] = 1;
                            }

                            $assignees[$assigneeId] = $assignee;
                        }
                    }
                }
            }
        }
    }

    foreach ($users as $user_id => $user) {
        if (count($user->reminders) === 0) {
            continue;
        }

        $html = array();
        $itemsIds = array();
        $hasAnyUpcomingDate = false;

        foreach ($user->reminders as $project_id => $reminders) {
            // Check if user doesn't want to receive notifications for the project.
            $meta = (array)get_post_meta($project_id, '_upstream_project_disable_notifications_' . $user_id);
            if (count($meta) > 0 && $meta[0] === 'on') {
                continue;
            }

            if (empty($html)) {
                $html = array(
                    '<p>'. sprintf(_x('Hello %s', '%s: User name', 'upstream-email-notifications'), $user->name) . ',</p>',
                    '<p>'. __('This email is to remind you about the following due dates:', 'upstream-email-notifications') .'</p>'
                );
            }

            $project = $projectsCache[$project_id];

            $html[] = '<fieldset>';

            $html[] = '<legend><h1><a href="'. $project->url .'">'. $project->title .'</a></h1></legend>';

            foreach ($itemsType as $itemType) {
                if (count($reminders[$itemType]) > 0) {
                    $hasAnyUpcomingDate = true;

                    $html[] = '<h3>'. $itemsLabelsMap[$itemType] .'</h3>';
                    $html[] = '<ul>';
                    foreach ($reminders[$itemType] as $item) {
                        if ($itemType === 'milestones') {
                            $itemTitle = $milestones[ $item->milestone ];
                            $itemEndDate = $item->end_date;
                        } else {
                            $itemTitle = $item->title;
                            $itemEndDate = isset($item->end_date) ? $item->end_date : $item->due_date;
                        }

                        $html[] = '<li>'. $itemTitle . ' (<span style="color: #E67E22">'. upstream_format_date( $itemEndDate ) .'</span>)</li>';
                        $itemsIds[] = $item->id;
                    }
                    $html[] = '</ul>';
                }
            }

            if ($reminders['past_due']['count'] > 0) {
                if ($hasAnyUpcomingDate) {
                    $html[] = '<hr />';
                }

                $html[] = '<h2>'. __('Past Due Dates', 'upstream-email-notifications') .'</h2>';
                foreach ($itemsType as $itemType) {
                    if (count($reminders['past_due'][$itemType]) === 0) {
                        continue;
                    }

                    $html[] = '<h3>'. $itemsLabelsMap[$itemType] .'</h3>';
                    $html[] = '<ul>';
                    foreach ($reminders['past_due'][$itemType] as $item) {
                        if ($itemType === 'milestones') {
                            $itemTitle = $milestones[ $item->milestone ];
                            $itemEndDate = $item->end_date;
                        } else {
                            $itemTitle = $item->title;
                            $itemEndDate = isset($item->end_date) ? $item->end_date : $item->due_date;
                        }

                        $html[] = '<li>'. $itemTitle . ' (<small>'. __('due date', 'upstream') .'</small> <span style="color: #E74C3C;">'. upstream_format_date( $itemEndDate ) .'</span>)</li>';
                        $itemsIds[] = $item->id;
                    }
                    $html[] = '</ul>';
                }
            }

            $html[] = '</fieldset>';
        }

        if (count($html) > 0) {
            $html[] = '<p>&mdash;</p><p><small>'. __('Please do not reply this message.', 'upstream-email-notifications') .'</small></p>';
            $emailBody = implode('', $html);

            try {
                $emailHasBeenSent = Plugin::doSendEmail($adminEmail, $user->email, $user->name, $emailSubject, $emailBody);
                if ($emailHasBeenSent) {
                    foreach ($user->reminders as $project_id => $reminders) {
                        $project = &$projects[$project_id];

                        foreach ($itemsType as $itemType) {
                            if (count($reminders[$itemType]) > 0) {
                                foreach ($reminders[$itemType] as $itemIndex => $item) {
                                    foreach ($item->reminders as $reminderIndex => $reminder) {
                                        $reminder = json_decode($reminder);
                                        $reminder->sent = true;
                                        $reminder->sent_at = $currentTimestamp;
                                        $item->reminders[$reminderIndex] = json_encode($reminder);
                                    }

                                    $reminders[$itemType][$itemIndex] = $item;
                                    $projects[$project_id]->{$itemType}[$item->id] = $item;
                                }
                            }
                        }

                        if ($reminders['past_due']['count'] > 0) {
                            foreach ($itemsType as $itemType) {
                                if (count($reminders['past_due'][$itemType]) > 0) {
                                    foreach ($reminders['past_due'][$itemType] as $itemIndex => $item) {
                                        foreach ($item->reminders as $reminderIndex => $reminder) {
                                            $reminder = json_decode($reminder);
                                            $reminder->sent = true;
                                            $reminder->sent_at = $currentTimestamp;
                                            $item->reminders[$reminderIndex] = json_encode($reminder);
                                        }

                                        $reminders['past_due'][$itemType][$itemIndex] = $item;
                                        $projects[$project_id]->{$itemType}[$item->id] = $item;
                                    }
                                }
                            }
                        }

                        foreach ($itemsType as $itemType) {
                            if (count($project->{$itemType}) > 0) {
                                array_walk($project->{$itemType}, function(&$item, $itemIndex) {
                                    $item = (array)$item;
                                });
                                update_post_meta($project->id, '_upstream_project_' . $itemType, array_values($project->{$itemType}));
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                continue;
            }
        }
    }
}