import { createIcons } from 'lucide';
import Activity from 'lucide/dist/esm/icons/activity.mjs';
import Archive from 'lucide/dist/esm/icons/archive.mjs';
import BadgeCheck from 'lucide/dist/esm/icons/badge-check.mjs';
import Building2 from 'lucide/dist/esm/icons/building-2.mjs';
import CalendarClock from 'lucide/dist/esm/icons/calendar-clock.mjs';
import ChartNoAxesColumnIncreasing from 'lucide/dist/esm/icons/chart-no-axes-column-increasing.mjs';
import ChevronDown from 'lucide/dist/esm/icons/chevron-down.mjs';
import ClipboardCheck from 'lucide/dist/esm/icons/clipboard-check.mjs';
import ClipboardPen from 'lucide/dist/esm/icons/clipboard-pen.mjs';
import ClipboardPlus from 'lucide/dist/esm/icons/clipboard-plus.mjs';
import Clock3 from 'lucide/dist/esm/icons/clock-3.mjs';
import Download from 'lucide/dist/esm/icons/download.mjs';
import FileDown from 'lucide/dist/esm/icons/file-down.mjs';
import LayoutDashboard from 'lucide/dist/esm/icons/layout-dashboard.mjs';
import ListChecks from 'lucide/dist/esm/icons/list-checks.mjs';
import LogOut from 'lucide/dist/esm/icons/log-out.mjs';
import Mail from 'lucide/dist/esm/icons/mail.mjs';
import MailWarning from 'lucide/dist/esm/icons/mail-warning.mjs';
import Menu from 'lucide/dist/esm/icons/menu.mjs';
import Monitor from 'lucide/dist/esm/icons/monitor.mjs';
import Moon from 'lucide/dist/esm/icons/moon.mjs';
import Pencil from 'lucide/dist/esm/icons/pencil.mjs';
import Plus from 'lucide/dist/esm/icons/plus.mjs';
import Save from 'lucide/dist/esm/icons/save.mjs';
import Search from 'lucide/dist/esm/icons/search.mjs';
import Settings from 'lucide/dist/esm/icons/settings.mjs';
import Sun from 'lucide/dist/esm/icons/sun.mjs';
import ThumbsDown from 'lucide/dist/esm/icons/thumbs-down.mjs';
import ThumbsUp from 'lucide/dist/esm/icons/thumbs-up.mjs';
import UserCheck from 'lucide/dist/esm/icons/user-check.mjs';
import UserCog from 'lucide/dist/esm/icons/user-cog.mjs';
import UserPlus from 'lucide/dist/esm/icons/user-plus.mjs';
import UserX from 'lucide/dist/esm/icons/user-x.mjs';
import Users from 'lucide/dist/esm/icons/users.mjs';
import WifiOff from 'lucide/dist/esm/icons/wifi-off.mjs';
import X from 'lucide/dist/esm/icons/x.mjs';

const icons = {
    Activity,
    Archive,
    BadgeCheck,
    BarChart3: ChartNoAxesColumnIncreasing,
    Building2,
    CalendarClock,
    ChevronDown,
    ClipboardCheck,
    ClipboardPen,
    ClipboardPlus,
    Clock3,
    Download,
    FileDown,
    LayoutDashboard,
    ListChecks,
    LogOut,
    Mail,
    MailWarning,
    Menu,
    Monitor,
    Moon,
    Pencil,
    Plus,
    Save,
    Search,
    Settings,
    Sun,
    ThumbsDown,
    ThumbsUp,
    UserCheck,
    UserCog,
    UserPlus,
    UserX,
    Users,
    WifiOff,
    X,
};

export function renderIcons() {
    createIcons({ icons });
}
